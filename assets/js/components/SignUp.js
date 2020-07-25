import React, { Component } from "react";
import { connect } from "react-redux";
import { makeStyles, withStyles } from '@material-ui/core/styles';

import { Link } from 'react-router-dom';
import SignInStyle from '../UI/SignIn/style';
import * as UI from '../UI/SignIn/base';

import { getError } from '../utils/Error';
import { register, resetSuccess } from '../actions/Authentification';
import { clearError } from '../actions/Error';

class SignUp extends React.Component {

  state = {
    firstname: '',
    lastname: '',
    email: '',
    password: ''
  }

  handleSubmit(event) {
    event.preventDefault();
    if (this.state.email == '' || this.state.password == '' || this.state.firstname == '' || this.state.lastname == '')
      return;
    this.props.register({ email: this.state.email, password: this.state.password, firstname: this.state.firstname, lastname: this.state.lastname });
  }

  handleChange = name => event => {
    this.setState({ [name]: event.target.value });
  }

  UNSAFE_componentWillReceiveProps(nextProps) {
    if (nextProps.success !== this.props.success) {
      if (nextProps.success) {
        this.props.history.push('/');

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
            Sign up
        </UI.Typography>
          <form className={classes.form} onSubmit={this.handleSubmit.bind(this)} action='#'>
            <UI.Grid container spacing={2}>
              <UI.Grid item xs={12} sm={6}>
                <UI.TextField
                  autoComplete="fname"
                  name="firstName"
                  variant="outlined"
                  required
                  fullWidth
                  id="firstName"
                  label="First Name"
                  autoFocus
                  onChange={this.handleChange('firstname').bind(this)}
                  error={getError(this.props, 'firstname')}
                  helperText={getError(this.props, 'firstname') ? getError(this.props, 'firstname') : ''}
                />
              </UI.Grid>
              <UI.Grid item xs={12} sm={6}>
                <UI.TextField
                  variant="outlined"
                  required
                  fullWidth
                  id="lastName"
                  label="Last Name"
                  name="lastName"
                  autoComplete="lname"
                  onChange={this.handleChange('lastname').bind(this)}
                  error={getError(this.props, 'lastname') ? true : false}
                  helperText={getError(this.props, 'lastname') ? getError(this.props, 'lastname') : ''}
                />
              </UI.Grid>
              <UI.Grid item xs={12}>
                <UI.TextField
                  variant="outlined"
                  required
                  fullWidth
                  id="email"
                  label="Email Address"
                  name="email"
                  autoComplete="email"
                  error={getError(this.props, 'email') ? true : false}
                  helperText={getError(this.props, 'email') ? getError(this.props, 'email') : ''}
                  onChange={this.handleChange('email').bind(this)}
                />
              </UI.Grid>
              <UI.Grid item xs={12}>
                <UI.TextField
                  variant="outlined"
                  required
                  fullWidth
                  name="password"
                  label="Password"
                  type="password"
                  id="password"
                  autoComplete="current-password"
                  error={getError(this.props, 'password') ? true : false}
                  helperText={getError(this.props, 'password') ? getError(this.props, 'password') : ''}
                  onChange={this.handleChange('password').bind(this)}
                />
              </UI.Grid>
            </UI.Grid>
            <UI.Button
              type="submit"
              fullWidth
              variant="contained"
              color="primary"
              className={classes.submit}
              onClick={this.handleSubmit.bind(this)}
            >
              Sign Up
          </UI.Button>
            <UI.Grid container justify="flex-end">
              <UI.Grid item>
                <Link to="/" variant="body2">
                  Already have an account? Sign in
              </Link>
              </UI.Grid>
            </UI.Grid>
            <UI.FormHelperText></UI.FormHelperText>
          </form>
        </div>
      </UI.Container>
    );
  }
}

const mapStateToProps = state => {
  return {
    error: state.Error.error,
    success: state.Authentification.success
  };
};

const registerStyle = withStyles(SignInStyle)(SignUp);

export default connect(mapStateToProps, { register, resetSuccess, clearError })(registerStyle);