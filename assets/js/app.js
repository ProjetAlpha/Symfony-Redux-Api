import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import App from './router/route';
import configureStore, { history } from "./store/index";
import { BrowserRouter } from "react-router-dom";
import { ConnectedRouter } from 'connected-react-router'
import 'semantic-ui-css/semantic.min.css';

import 'draft-js/dist/Draft.css';
import 'draft-js-emoji-plugin/lib/plugin.css';
import 'draft-js-linkify-plugin/lib/plugin.css';
import 'draft-js-undo-plugin/lib/plugin.css';
import 'draft-js-hashtag-plugin/lib/plugin.css';
import 'draft-js-image-plugin/lib/plugin.css';
import 'draft-js-static-toolbar-plugin/lib/plugin.css';
import 'draft-js-focus-plugin/lib/plugin.css';

const store = configureStore(/* provide initial state if any */);

ReactDOM.render(
    <Provider store={store}>
      <ConnectedRouter history={history}>
        { /* location change listener doesn't work with <BrowserRouter> */ }
          <App />
        { /* </BrowserRouter> */ }
      </ConnectedRouter>
    </Provider>,
  document.getElementById('root')
);