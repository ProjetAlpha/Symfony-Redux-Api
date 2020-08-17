import axios from "axios";
import * as Auth from '../utils/Authentification';
import { logoutOnResponseError } from '../utils/Authentification';


/**
 * Base api requests configuration.
 *
 * @return  void
 */
const client = axios.create({
  baseURL: process.env.NODE_ENV == 'production' ? process.env.PROD_API_BASEURL : process.env.DEV_API_BASEURL,
  headers: {
    "Content-Type": "application/json",
    "X-Requested-With": "XMLHttpRequest"
  }
});

client.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
client.defaults.headers.common['X-API-TOKEN'] = Auth.getUser()['token'] || null;

let apiReq = [];

/**
 * Response middleware to refresh api token automatically.
 *
 * @param   response 
 * @param   response
 * @param   error
 * 
 * @return  void
 */
client.interceptors.response.use(response => {
  return response;
}, error => {
  if (error.response && (error.response.status === 401 || error.response.status === 403)) {
    if (error.response.status === 401) {
      if (error.response.data && error.response.data.refresh_token) {
        client.get(`/public/token/refresh/${error.response.data.refresh_token}`)
              .then(res => {
                Auth.setAuthToken(client, res.data.token);
                Auth.updateUser('token', res.data.token);
                
                // retry the current request with a fresh api token
                error.config.headers['X-API-TOKEN'] = res.data.token;
                return client.request(error.config);
              })
              .catch(err => {
                logoutOnResponseError(error.response.status);
                window.location = '/';
              })
      } else {
        logoutOnResponseError(error.response.status);
        window.location = '/';
      }
    } else if (error.response.status === 403) {
      logoutOnResponseError(error.response.status);
      window.location = '/';
    }
  } else {
    return Promise.reject(error);
  }
});

client.interceptors.request.use(request => {
 return request;
}, error => {
  return Promise.reject(error);
});

export default client;