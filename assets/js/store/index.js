import { createStore, applyMiddleware } from 'redux';
import { composeWithDevTools } from 'redux-devtools-extension';
import { createBrowserHistory } from 'history';
import { routerMiddleware } from 'connected-react-router';
import thunk from 'redux-thunk';
import reducers from '../reducers/index';

export const history = createBrowserHistory();

/*export default process.env.NODE_ENV == 'production' 
    ? createStore(reducers(history), applyMiddleware(thunk))
    : createStore(reducers, composeWithDevTools(
  applyMiddleware(thunk)
));*/

export default function configureStore(preloadedState) {
  const store = createStore(
    reducers(history), // root reducer with router state
    preloadedState,
    process.env.NODE_ENV == 'production' ? compose(
      applyMiddleware(
        routerMiddleware(history), // for dispatching history actions
        thunk
      ))
      : composeWithDevTools(
        applyMiddleware(routerMiddleware(history), thunk)
      ),
  )

  return store
}