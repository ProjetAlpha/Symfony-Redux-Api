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
import ResetPassword from '../components/ResetPassword';
import ResetPasswordLink from '../components/ResetPasswordLink';

import NotFound from '../components/main/NotFound';
import { withStyles } from '@material-ui/core/styles';
import { withRouter } from 'react-router-dom';
import { body } from '../UI/main';

import * as Auth from '../utils/Authentification';

/*
        <PrivateRoute component={dashboard} path="/" exact />
*/

class App extends Component {

  render() {
    const classes = this.props.classes;

    return (
      <div className={classes.root}>
        <div className={classes.toolbar} />
        <TopAppBar></TopAppBar>
        <Switch>
          <AdminRoute restricted={true} component={Admin} path="/admin" exact />
          
          <PublicRoute restricted={true} component={SignIn} path="/" exact />
          <PublicRoute restricted={true} component={SignUp} path="/register" exact />
          <PublicRoute restricted={true} component={ResetPassword} path="/resetPassword" exact />
          <PublicRoute restricted={true} component={ResetPasswordLink} path="/resetPassword/link/:id" exact />
          
          <PrivateRoute component={() => <Profil id={Auth.getUser().id}></Profil>} path="/profil" exact />
          <Route component={NotFound} />
        </Switch>
      </div>
    );
  }
}

const appStyle = withStyles(body)(App);

export default withRouter(appStyle);