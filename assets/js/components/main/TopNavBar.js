import React from 'react';
import { Link } from 'react-router-dom';
import { connect } from "react-redux";
import clsx from 'clsx';

import * as Auth from '../../utils/Authentification';
import * as UI from '../../UI/NavBar/base';

import NavBarStyle from '../../UI/NavBar/style';
import { logout } from '../../actions/Authentification';
import { makeStyles, withStyles, withTheme } from '@material-ui/core/styles';

class TopAppBar extends React.Component {

  state = {
    mobileOpen: false,
    desktopOpen: window.innerWidth >= 600 ? true : false
  }

  handleLogout() {
    Auth.logout();
    this.props.logout();
  }

  handleDrawerToggle() {
    // weird bug fix with material-ui hidden component.
    this.setState(prevState => ({
      mobileOpen: window.innerWidth >= 600 ? false : !prevState.mobileOpen,
      desktopOpen: window.innerWidth >= 600 ? !prevState.desktopOpen : false
    }));

    if (window.innerWidth >= 600) {
      let root = document.documentElement;
      root.style.setProperty('--barpad', !this.state.desktopOpen ? '240px' : 0);
    }
  }

  render() {
    const classes = this.props.classes;
    const container = window !== undefined ? () => window.document.body : undefined;

    const drawer = (
      <div>
        <div className={classes.drawerHeader}>
          <UI.IconButton onClick={this.handleDrawerToggle.bind(this)}>
            {this.props.theme.direction === 'ltr' ? <UI.ChevronLeftIcon /> : <UI.ChevronRightIcon />}
          </UI.IconButton>
        </div>
        <div className={classes.toolbar} />
        <UI.Divider />
        <UI.List>

          {
            Auth.isAdmin() && <Link to="/admin">
              <UI.ListItem button>
                <UI.ListItemIcon>
                  <UI.PeopleIcon></UI.PeopleIcon>
                </UI.ListItemIcon>
                <UI.ListItemText primary={'Users managment'} />
              </UI.ListItem>
            </Link>
          }

          {
            Auth.isAdmin() &&
            <Link to="#">
              <UI.ListItem button>
                <UI.ListItemIcon>
                  <UI.CreateIcon></UI.CreateIcon>
                </UI.ListItemIcon>
                <UI.ListItemText primary={'Articles managment'} />
              </UI.ListItem>
            </Link>
          }
        </UI.List>
      </div>
    );

    return (
      <div className={classes.root}>
        <UI.AppBar position="fixed" className={clsx(classes.appBar, {
          [classes.appBarShift]: !this.state.desktopOpen
        })}>
          <UI.Toolbar>
            <UI.IconButton edge="start" className={clsx(classes.menuButton, (this.state.desktopOpen || this.state.mobileOpen) && classes.hide)} color="inherit" aria-label="menu" onClick={this.handleDrawerToggle.bind(this)}>
              <UI.MenuIcon />
            </UI.IconButton>
            <UI.Typography variant="h6" className={classes.title}>
              <Link className={classes.link} to="/">Home</Link>
            </UI.Typography>

            <div className={classes.rightSideContainer}>
              {
                !Auth.isLogin() && <Link to="/register"><UI.Button className={classes.buttonGreen}> Register </UI.Button></Link>
              }

              {
                !Auth.isLogin() && <Link to="/"><UI.Button className={classes.buttonBlue}> Login </UI.Button></Link>
              }

              {
                Auth.isLogin() && this.props.user
                && <Link to="/profil">
                  <UI.Button className={classes.buttonGreen}> {this.props.user.firstname} </UI.Button>
                </Link>
              }

              {
                Auth.isLogin() && <Link to="/" onClick={() => this.handleLogout()}>
                  <UI.Button className={classes.buttonBlue}> Logout </UI.Button>
                </Link>
              }

              <UI.IconButton
                edge="end"
                aria-label="settings of current user"
                color="inherit"
              >
                <UI.SettingsIcon />
              </UI.IconButton>
            </div>
          </UI.Toolbar>
        </UI.AppBar>
        <nav className={classes.drawer} aria-label="mailbox folders">
          {/* The implementation can be swapped with js to avoid SEO duplication of links. */}
          <UI.Hidden smUp implementation="css">
            <UI.Drawer
              container={container}
              variant="temporary"
              anchor={"left"}
              open={this.state.mobileOpen}
              onClose={this.handleDrawerToggle.bind(this)}
              classes={{
                paper: classes.drawerPaper,
              }}
              ModalProps={{
                keepMounted: true, // Better open performance on mobile.
              }}
            >
              {drawer}
            </UI.Drawer>
          </UI.Hidden>
          <UI.Hidden xsDown implementation="css">
            <UI.Drawer
              classes={{
                paper: classes.drawerPaper,
              }}
              anchor={"left"}
              variant="persistent"
              open={this.state.desktopOpen}
            >
              {drawer}
            </UI.Drawer>
          </UI.Hidden>
        </nav>
      </div>
    );
  }
}

const mapStateToProps = state => {
  return {
    user: state.Authentification.user
  };
};

const AppBarTheme = withTheme(TopAppBar);

const AppBarStyle = withStyles(NavBarStyle)(AppBarTheme);

export default connect(mapStateToProps, { logout })(AppBarStyle);