import axios from "axios";

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

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Server error middleware.
 *
 * @param   Error
 *
 * @return  Promise
 */
axios.interceptors.response.use(undefined, (err) => {
  return new Promise(() => {
    if (err.response.status === 401) {
      // unauthorized request, redirect to login form
      window.location.href = client.baseURL;
    } else if (err.response.status === 500) {
      document.open();
      document.write(err.response.data);
      document.close();
    }
    throw err;
  });
});

export default client;