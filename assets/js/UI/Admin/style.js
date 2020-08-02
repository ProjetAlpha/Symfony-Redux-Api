import red from '@material-ui/core/colors/red';

export default theme => ({
    root: {
        //display: 'flex',
        //justifyContent:'center',
        margin: theme.spacing(2),
        backgroundColor: theme.palette.background.paper,
    },
    toolbar: theme.mixins.toolbar,
    card: {
        marginTop: theme.spacing(1)
    },
    details: {
        display: 'flex',
        flexDirection: 'column',
    },
    content: {
        flexGrow: 1,
        backgroundColor: theme.palette.background.default,
        padding: theme.spacing(3),
    },
    inline: {
        display: 'inline',
    },
    redIcon: {
        color: red[500],
        cursor: 'pointer'
    },
})