import React, { Component } from "react";
import { connect } from "react-redux";

import * as UI from '../UI/Login/base';
import { Link } from 'react-router-dom';
import LoginStyle from '../UI/Login/style';
import { login } from '../actions/Authentification';

class SignIn extends React.Component {

  state = {
    email: '',
    password: ''
  }

  handleSubmit() {
    if (this.state.email !== '' && this.state.password !== '')
      this.props.login({ email: this.state.email, password: this.state.password });
  }

  handleChange = name => event =>  {
    this.setState({ [name]: event.target.value });
  }

  render() {
    const classes = LoginStyle();

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
          <div className={classes.form}>
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
              onChange={ this.handleChange('email').bind(this) }
              error={ this.state.error ? true : false }
              helperText={ this.state.error ? 'Incorrect email' : '' }
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
              onChange={ this.handleChange('password').bind(this) }
              error={ this.state.error ? true : false }
              helperText={ this.state.error ? 'Incorrect password' : '' }
            />
            <UI.FormControlLabel
              control={<UI.Checkbox value="remember" color="primary" />}
              label="Remember me"
            />
            <UI.Button
              type="submit"
              fullWidth
              variant="contained"
              color="primary"
              className={ classes.submit }
              onClick={ this.handleSubmit.bind(this) }
            >
              Sign In
            </UI.Button>
            <UI.Grid container>
              <UI.Grid item xs>
                <Link to="#" variant="body2">
                  Forgot password?
                </Link>
              </UI.Grid>
              <UI.Grid item>
                <Link to={"/register"} variant="body2">
                  {"Don't have an account? Sign Up"}
                </Link>
              </UI.Grid>
            </UI.Grid>
          </div>
        </div>
      </UI.Container>
    );
  }
}

const mapStateToProps = state => {
  return {
    error: state.error
  };
};

export default connect(mapStateToProps, { login })(SignIn);