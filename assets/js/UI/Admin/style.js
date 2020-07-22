export default theme => ({
    root: {
        display: 'flex',
        alignItems: 'center',
        flexDirection:'column',
        padding: theme.spacing(0, 1),
        // necessary for content to be below app bar
        justifyContent: 'flex-end',
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
    }
})