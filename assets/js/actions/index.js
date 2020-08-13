import axios from "axios";
import { logoutOnResponseError } from '../utils/Authentification';


axios.defaults.withCredentials = true;
/**
 * Base api requests configuration.
 *
 * @return  void
 */
const client = axios.create({
  baseURL: process.env.NODE_ENV == 'production' ? process.env.PROD_API_BASEURL : process.env.DEV_API_BASEURL,
  withCredentials: true,
  headers: {
    "Content-Type": "application/json",
    "X-Requested-With": "XMLHttpRequest"
  }
});

// required for cross origin api request, otherwise session id is missing.
client.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let apiReq = [];

/**
 * Logout a user when a server session has expired.
 *
 * @param   response 
 * @param   response
 * @param   error
 * 
 * @return  void
 */
client.interceptors.response.use(response => {
  // apiReq[route] = { resolved: true, hasError: false }
  return response;
}, error => {
  // 401 & token_expired: true
  // refresh_token: XXX
  // /api/token/refresh
  // delete refresh token & new api token & send new access token
  // si refresh_token || api_token => check DB
  
  if (error.response && (error.response.status === 401 || error.response.status === 403)) {
    if (error.response.status === 401) {
      if (error.response.data && error.response.data.refreshToken) {
        client.post(`/token/refresh/${error.response.data.refreshToken}`)
              .then(res => {
                // New access_token
                // Auth.setUser({})
              })
      }
    }

    if (error.response.status === 403) {
      logoutOnResponseError(error.response.status);
      window.location = '/';
    }
  } else {
    // apiReq[route].hasError = { resolved: false, hasError: true }
    return Promise.reject(error);
  }
});

client.interceptors.request.use(request => {
  /*
    // wait server response for a specified route before making a new request.
    if (apiReq[route].resolved === false && !apiReq[route].hasError) return;
    
    if (apiReq[route]) {
      apiReq[route] = { resolved: false, hasError: false }
    } else {
      apiReq[route].resolved = false;
      apiReq[route].hasError = false;
    }
  */
 return request;
}, error => {
  // apiReq[route].hasError = true;
  return Promise.reject(error);
});

export default client;