export default theme => ({
    root: {
       maringTop:theme.spacing(2)
    },

    pagination: {
        marginTop: theme.spacing(2),
        [theme.breakpoints.down('sm')]: {
            margin:'0'
        }
    },
});