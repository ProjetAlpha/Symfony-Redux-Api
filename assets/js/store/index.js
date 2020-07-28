import { createStore, applyMiddleware } from 'redux';
import { composeWithDevTools } from 'redux-devtools-extension';
import thunk from 'redux-thunk';
import reducers from '../reducers/index';

// TODO: axios interceptors when credentials expire => delete localstorage user.

export default process.env.NODE_ENV == 'production' 
    ? createStore(reducers, applyMiddleware(thunk))
    : createStore(reducers, composeWithDevTools(
  applyMiddleware(thunk)
));