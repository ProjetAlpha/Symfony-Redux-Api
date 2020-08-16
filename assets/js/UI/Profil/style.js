export default theme => ({
    root: {
        margin: theme.spacing(2),
        backgroundColor: theme.palette.background.paper,
    },
    profil: {
      height: '100%'
    },
    avatar: {
      margin: theme.spacing(1),
      backgroundColor: theme.palette.secondary.main,
    },
    form: {
      width: '100%', // Fix IE 11 issue.
      marginTop: theme.spacing(1),
    },
    submit: {
      margin: theme.spacing(3, 0, 2),
    },
    item: {
      margin: theme.spacing(1)
    }
});