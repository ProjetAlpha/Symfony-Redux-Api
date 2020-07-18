import React from 'react';
import { Link } from 'react-router-dom';
import { connect } from "react-redux";
import * as Auth from '../../utils/Authentification';
import * as UI from '../../UI/NavBar/base';
import NavBarStyle from '../../UI/NavBar/style';
import { logout } from '../../actions/Authentification';
import { makeStyles, withStyles } from '@material-ui/core/styles';

class TopAppBar extends React.Component {

  handleLogout() {
    Auth.logout();
    this.props.logout();
  }

  render() {
  const classes = this.props.classes;

  return (
    <div className={classes.root}>
      <UI.AppBar position="static">
        <UI.Toolbar>
          <UI.IconButton edge="start" className={classes.menuButton} color="inherit" aria-label="menu">
            <UI.MenuIcon />
          </UI.IconButton>
            <UI.Typography variant="h6" className={classes.title}>
              <Link className={classes.link} to="/">Home</Link>
            </UI.Typography>
          
          <span>
          {
            !Auth.isLogin() && <Link to="/register" className={classes.link}><UI.Button color="inherit"> Register </UI.Button></Link>
          }
  
          {
            !Auth.isLogin() && <Link to="/" className={classes.link}><UI.Button color="inherit"> Login </UI.Button></Link>
          }

          {
            Auth.isLogin() && this.props.user
            && <Link to="#" className={classes.link}>
                <UI.Button color="inherit"> { this.props.user.firstname } </UI.Button>
              </Link>
          }

          {
            Auth.isLogin() && <Link to="/" onClick={ () => this.handleLogout() } className={classes.link}>
              <UI.Button color="inherit"> Logout </UI.Button>
            </Link>
          }
          </span>
          
          <UI.IconButton
              edge="end"
              aria-label="settings of current user"
              color="inherit"
            >
              <UI.SettingsIcon />
            </UI.IconButton>
        </UI.Toolbar>
      </UI.AppBar>
    </div>
  );
  }
}

const mapStateToProps = state => {
  return {
    user: state.Authentification.user
  };
};

const AppBarStyle = withStyles(NavBarStyle)(TopAppBar);

export default connect(mapStateToProps, { logout })(AppBarStyle);