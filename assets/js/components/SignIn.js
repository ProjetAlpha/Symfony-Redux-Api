import React, { Component } from "react";
import { connect } from "react-redux";
import { makeStyles, withStyles } from '@material-ui/core/styles';

import * as UI from '../UI/Login/base';
import { Link } from 'react-router-dom';
import LoginStyle from '../UI/Login/style';
import { login, resetSuccess } from '../actions/Authentification';
import { clearError } from '../actions/Error';
import { getError } from "../utils/Error";

class SignIn extends React.Component {

  state = {
    email: '',
    password: ''
  }

  handleSubmit(event) {
    event.preventDefault();
    if (this.state.email !== '' && this.state.password !== '') {
      this.props.login({ email: this.state.email, password: this.state.password });
    }
  }

  handleChange = name => event => {
    this.setState({ [name]: event.target.value });
  }

  UNSAFE_componentWillReceiveProps(nextProps) {
    if (nextProps.success !== this.props.success) {
      if (nextProps.success) {
        // TODO : user dashboard.
        this.props.history.push(nextProps.user && nextProps.user.isAdmin ? '/admin' : '/profil');

        // clear error and reset register success.
        this.props.resetSuccess();
        this.props.clearError();
      }
    }
  }

  render() {
    const classes = this.props.classes;

    return (
      <UI.Container component="main" maxWidth="xs">
        <UI.CssBaseline />
        <div className={classes.paper}>
          <UI.Avatar className={classes.avatar}>
            <UI.LockOutlinedIcon />
          </UI.Avatar>
          <UI.Typography component="h1" variant="h5">
            Sign in
          </UI.Typography>
          <form className={classes.form} action="#" onSubmit={this.handleSubmit.bind(this)}>
            <UI.TextField
              variant="outlined"
              margin="normal"
              required
              fullWidth
              id="email"
              label="Email Address"
              name="email"
              autoComplete="email"
              autoFocus
              onChange={this.handleChange('email').bind(this)}
            />
            <UI.TextField
              variant="outlined"
              margin="normal"
              required
              fullWidth
              name="password"
              label="Password"
              type="password"
              id="password"
              autoComplete="current-password"
              onChange={this.handleChange('password').bind(this)}
            />

            {
              getError(this.props) &&
              <UI.Alert severity="error">
                <UI.AlertTitle>Error</UI.AlertTitle>
                {getError(this.props)}
              </UI.Alert>
            }

            <UI.Button
              type="submit"
              fullWidth
              variant="contained"
              color="primary"
              className={classes.submit}
              onClick={this.handleSubmit.bind(this)}
            >
              Sign In
            </UI.Button>
            <UI.Grid container>
              <UI.Grid item xs>
                <Link to="/resetPassword" variant="body2">
                  Forgot password?
                </Link>
              </UI.Grid>
              <UI.Grid item>
                <Link to={"/register"} variant="body2">
                  {"Don't have an account? Sign Up"}
                </Link>
              </UI.Grid>
            </UI.Grid>
          </form>
        </div>
      </UI.Container>
    );
  }
}

const mapStateToProps = state => {
  return {
    error: state.Error.error,
    success: state.Authentification.success,
    user: state.Authentification.user
  };
};

const loginStyle = withStyles(LoginStyle)(SignIn);

export default connect(mapStateToProps, { login, resetSuccess, clearError })(loginStyle);