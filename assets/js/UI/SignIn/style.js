export default theme => ({
  paper: {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
  },
  loginContainer: {
    display: 'flex',
    justifyContent:'center',
    alignItems:'center',
    /* haut | droit | bas | gauche */
    margin: '15px auto 0 auto',
    height:'87vh',
    [theme.breakpoints.down('xs')]: {
      height:'100vh',
      margin:0
    }
  },
  avatar: {
    margin: theme.spacing(1),
    backgroundColor: theme.palette.secondary.main,
  },
  form: {
    width: '100%', // Fix IE 11 issue.
    marginTop: theme.spacing(3),
  },
  submit: {
    margin: theme.spacing(3, 0, 2),
  },
});