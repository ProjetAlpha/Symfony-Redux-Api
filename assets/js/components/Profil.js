import React, { Component } from "react";
import { connect } from "react-redux";
import { makeStyles, withStyles } from '@material-ui/core/styles';

import * as UI from '../UI/Profil/base';

import ProfilStyle from '../UI/Profil/style';
import { getProfilData } from '../actions/Profil';
import { normalizeData } from '../utils/Normalize';

class Profil extends React.Component {
    
    state = {
      user: {}
    }

    componentDidMount() {
      const user = this.props.user.id == this.props.id ? this.props.user
      : this.props.getProfilData({ id: this.props.id });

      const normalizedKeys = {
        email: 'Email',
        firstname: 'Firstname',
        lastname: 'Lastname'
      };

      this.setState({
        user: normalizeData(user, normalizedKeys)
      })
    }

    render () {
        const classes = this.props.classes;

        return (
            <UI.Container component="main" maxWidth="xs">
              <div className={classes.profil}>
                <UI.List component="nav" aria-label="user profil info">
                {
                  this.state.user && 
                  Object.entries(this.state.user).map(([key,value], i) =>
                    <UI.ListItem  button key={i}>
                      <UI.ListItemText className={classes.item} primary={key}/>
                      <UI.ListItemText className={classes.item} secondary={value}/>
                    </UI.ListItem>
                  )
                }
                </UI.List>
              </div>
            </UI.Container>
        );
    }
}

const mapStateToProps = state => {
    return {
      error: state.Error.error,
      user: state.Authentification.user
    };
  };
  
const profilStyle = withStyles(ProfilStyle)(Profil);
  
export default connect(mapStateToProps, { getProfilData })(profilStyle);
