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
import AdminRoute from './AdminRoute';

import SignIn from '../components/SignIn';
import SignUp from '../components/SignUp';
import Profil from '../components/Profil';
import Admin from '../components/Admin';

import * as Auth from '../utils/Authentification';

/*
        <PrivateRoute component={dashboard} path="/" exact />
*/

export default function App() {
  return (
    <BrowserRouter>
        <TopAppBar></TopAppBar>
        <Switch>
          <AdminRoute restricted={true} component={Admin} path="/admin" exact />
          <PublicRoute restricted={true} component={SignIn} path="/" exact />
          <PublicRoute restricted={true} component={SignUp} path="/register" exact />
          <PrivateRoute component={ () => <Profil id={ Auth.getUser().id }></Profil> } path="/profil" exact />
        </Switch>
    </BrowserRouter>
  );
}