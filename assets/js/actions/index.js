import axios from "axios";
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

/**
 * Logout a user when a server session has expired.
 *
 * @param   response 
 * @param   response
 * @param   error
 * 
 * @return  void
 */
client.interceptors.response.use(response => response, error => {
  if (error.response && (error.response.status === 401 || error.response.status === 403)) {
    logoutOnResponseError(error.response.status);
    window.location = '/';
  } else {
    return Promise.reject(error);
  }
});

export default client;