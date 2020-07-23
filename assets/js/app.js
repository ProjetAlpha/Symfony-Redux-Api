import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import App from './router/route';
import store from "./store/index"
import { BrowserRouter } from "react-router-dom";
import 'semantic-ui-css/semantic.min.css';

ReactDOM.render(
    <Provider store={store}>
      <BrowserRouter>
        <App />
      </BrowserRouter>
    </Provider>,
  document.getElementById('root')
);