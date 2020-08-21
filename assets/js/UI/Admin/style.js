import red from '@material-ui/core/colors/red';

export default theme => ({
    root: {
        //display: 'flex',
        //justifyContent:'center',
        marginLeft: '4%',
        marginRight: '4%',
        marginTop: '2%',
        marginBottom: '3%',
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
    searchItem: {
        [theme.breakpoints.up('sm')]: {
            justifyContent:'flex-start'
        },
        marginBottom:theme.spacing(1),
        justifyContent:'center!important'
    },
    mobile_row: {
        [theme.breakpoints.down('md')]: {
            display:'flex',
            flexDirection: 'row'
        }
    },
    img: {
        //width: '100%',
        //height: 'auto',
        maxWidth: '100%',
        maxHeight: '190px'
    },
    imgContainer: {
        textAlign: 'center',
        display: 'flex',
        justifyContent: 'space-around',
        alignItems: 'center'
    },
    header: {
        marginBottom: theme.spacing(3)
    },
    circularContainer: {
        display:'flex',
        alignItems:'center',
        justifyContent:'center',
        height:'100%'
    },
    paper: {
        width: 'auto',
        minHeight: '100px',
        maxHeight: '190px'
    }
})