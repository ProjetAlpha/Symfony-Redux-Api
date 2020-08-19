import { withTheme } from "@material-ui/core";
import { pad, colors, buttons } from '../main';

const drawerWidth = 250;

export default theme => ({
    root: {
      /*flexGrow: 1*/
    },
    drawer: {
      [theme.breakpoints.up('sm')]: {
        width: drawerWidth,
        flexShrink: 0,
      }
    },
    toolbar: theme.mixins.toolbar,
    drawerPaper: {
      width: drawerWidth,
    },
    appBar: {
      backgroundColor:colors.lightBlue2,
      [theme.breakpoints.up('sm')]: {
        width: `calc(100% - ${drawerWidth}px)`,
        marginLeft: drawerWidth,
      },
      transition: theme.transitions.create(['margin', 'width'], {
        easing: theme.transitions.easing.sharp,
        duration: theme.transitions.duration.leavingScreen,
      })
    },
    appBarShift: {
      width: '100%'
    },
    hide: {
      display: 'none',
    },
    menuButton: {
      marginRight: theme.spacing(2)
    },
    title: {
      flexGrow: 1,
    },
    toolBarButtons: {
        marginLeft:"auto"
    },
    link: {
        '&:hover': {
          color:'#00e2fa'
        },
        color: '#ffffff',
        TextDecoration: "inherit" 
    },
    drawerContainer: {
      width : '100%',
    },
    drawerHeader: {
      width: '100%',
      display: 'flex',
      marginTop: '12px',
      justifyContent: 'flex-end'
    },
    rightSideContainer: {
      marginLeft:'auto'
    },
    buttonGreen: {
      ...buttons.green(theme)
    },
    buttonBlue: {
      ...buttons.blue(theme)
    }
});
