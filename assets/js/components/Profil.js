import React, { Component } from "react";
import { connect } from "react-redux";
import { makeStyles, withStyles } from '@material-ui/core/styles';

import * as UI from '../UI/Profil/base';
import { Link } from 'react-router-dom';
// import LoginStyle from '../UI/Login/style';
import { getProfilData } from '../actions/Profil';

class Profil extends React.Component {

    state = {
        
    }

    render () {
        return (
            
        );
    }
}

const mapStateToProps = state => {
    return {
      error: state.error,
      user: state.Authentification.user
    };
  };
  
// responsive container flex box if needed...
// const loginStyle = withStyles(LoginStyle)(SignIn);
  
export default connect(mapStateToProps, { getProfilData })(Profil);
