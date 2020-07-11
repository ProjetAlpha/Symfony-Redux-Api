import React, { Component } from "react";
import { connect } from "react-redux";

import { Link } from 'react-router-dom';
import SignInStyle from '../UI/SignIn/style';
import * as UI from '../UI/SignIn/base';

import { register } from '../actions/Authentification';

class SignUp extends React.Component {

  state = {
    firstname: '',
    lastname: '',
    email: '',
    password: ''
  }

  handleSubmit() {
    const isEmpty = Object.values(this.state).every(x => (x === null || x === ''));
    
    if (!isEmpty)
      this.props.register({ email: this.state.email, password: this.state.password });
  }

  handleChange = name => event =>  {
    this.setState({ [name]: event.target.value });
  }

  render () {
    const classes = SignInStyle();

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
        <form className={classes.form} noValidate>
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
                onChange={this.handleChange.bind(this)}
                error={ this.state.error ? true : false }
                helperText={ this.state.error ? 'Incorrect firstname' : '' }
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
                onChange={ this.handleChange.bind(this) }
                error={ this.state.error ? true : false }
                helperText={ this.state.error ? 'Incorrect lastname' : '' }
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
                error={ this.state.error ? true : false }
                helperText={ this.state.error ? 'Incorrect email' : '' }
                onChange={ this.handleChange.bind(this) }
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
                error={ this.state.error ? true : false }
                helperText={ this.state.error ? 'Incorrect password' : '' }
                onChange={ this.handleChange.bind(this) }
              />
            </UI.Grid>
            <UI.Grid item xs={12}>
              <UI.FormControlLabel
                control={<UI.Checkbox value="allowExtraEmails" color="primary" />}
                label="I want to receive inspiration, marketing promotions and updates via email."
              />
            </UI.Grid>
          </UI.Grid>
          <UI.Button
            type="submit"
            fullWidth
            variant="contained"
            color="primary"
            className={classes.submit}
            onSubmit={this.handleSubmit.bind(this)}
          >
            Sign Up
          </UI.Button>
          <UI.Grid container justify="flex-end">
            <UI.Grid item>
              <Link to={"/"} variant="body2">
                Already have an account? Sign in
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
    error: state.error
  };
};

export default connect(mapStateToProps, { register })(SignUp);