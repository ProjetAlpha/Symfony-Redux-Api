/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../../css/app.css';
import React, { Component, PropTypes } from 'react';
import TopAppBar from '../components/main/TopNavBar';
import { BrowserRouter, Route, Switch, Link } from 'react-router-dom'

import PrivateRoute from './PrivateRoute';
import PublicRoute from './PublicRoute';

import SignIn from '../components/SignIn';
import SignUp from '../components/SignUp';

/*
        <PrivateRoute component={Dashboard} path="/dashboard" exact />
        <PrivateRoute component={Settings} path="/settings" exact />
*/

export default function App() {
  return (
    <BrowserRouter>
        <TopAppBar></TopAppBar>
        <Switch>
          <PublicRoute restricted={false} component={SignIn} path="/" exact />
          <PublicRoute restricted={false} component={SignUp} path="/register" exact />
        </Switch>
    </BrowserRouter>
  );
}