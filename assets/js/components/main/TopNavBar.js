import React from 'react';
import { Link } from 'react-router-dom';
import { connect } from "react-redux";
import clsx from 'clsx';

import * as Auth from '../../utils/Authentification';
import * as UI from '../../UI/NavBar/base';

import DrawerContent from './Drawer';
import NavBarStyle from '../../UI/NavBar/style';
import { logout } from '../../actions/Authentification';
import { withStyles, withTheme } from '@material-ui/core/styles';

class TopAppBar extends React.Component {

  state = {
    mobileOpen: false,
    desktopOpen: window.innerWidth >= 960 ? true : false
  }

  handleResize() {
    let root = document.documentElement;
    if (window.innerWidth < 960 && this.state.desktopOpen) {
      this.setState({
        desktopOpen: false
      });
      root.style.setProperty('--barpad', 0);
    }

    if (window.innerWidth >= 960 && !this.state.desktopOpen) {
      this.setState({
        desktopOpen: true
      });
      root.style.setProperty('--barpad', '240px');
    }
  }

  componentDidMount() {
    window.addEventListener('resize', this.handleResize.bind(this));
  }

  componentWillUnmount() {
    window.removeEventListener('resize', this.handleResize.bind(this));
  }

  handleLogout() {
    Auth.logout();
    this.props.logout();
  }

  handleDrawerToggle() {
    // weird bug fix with material-ui hidden component.
    this.setState(prevState => ({
      mobileOpen: window.innerWidth >= 960 ? false : !prevState.mobileOpen,
      desktopOpen: window.innerWidth >= 960 ? !prevState.desktopOpen : false
    }));

    if (window.innerWidth >= 960) {
      let root = document.documentElement;
      root.style.setProperty('--barpad', !this.state.desktopOpen ? '240px' : 0);
    }
  }

  render() {
    const classes = this.props.classes;
    const container = window !== undefined ? () => window.document.body : undefined;

    const drawerContent = (
      <div className={classes.drawerContainer}>
        <div className={classes.drawerHeader}>
          <UI.IconButton onClick={this.handleDrawerToggle.bind(this)}>
            {this.props.theme.direction === 'ltr' ? <UI.ChevronLeftIcon /> : <UI.ChevronRightIcon />}
          </UI.IconButton>
        </div>
        <DrawerContent></DrawerContent>
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
              {
                /*<UI.IconButton
                  edge="end"
                  aria-label="settings of current user"
                  color="inherit"
                >
                  <UI.SettingsIcon />
                </UI.IconButton>*/
              }
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
              {drawerContent}
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
              {drawerContent}
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