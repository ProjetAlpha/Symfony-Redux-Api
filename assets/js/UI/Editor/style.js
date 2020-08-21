export default theme => ({
    flex: {
        margin: '3%',
    },
    customInlineButton: {
        display: 'inline-block',
        backgroundColor: '#fff',
        '& button': {
            backgroundColor: '#fff!important',
            verticalAlign: 'bottom',
            lineHeight: '1em',
            width: '1.5em',
            border: '0',
            color: '#888!important'
        }
    },
    editor: {
        minHeight: '300px',
        boxSizing: 'border-box',
        border: '1px solid #ddd',
        cursor: 'text',
        padding: '16px',
        borderRadius: '2px',
        marginBottom: '2em',
        background: '#fefefe'
    },
    btnCenter: {
        marginTop:'10px',
        textAlign:'center'
    },
    header: {
        display:'flex',
        flexDirection:'column'
    },
    headerItems: {
        backgroundColor: theme.palette.background.paper,
        marginTop:'12px',
        marginBottom:'12px',
        padding:'15px'
    },
    mr_l_t_15_mobile: {
        [theme.breakpoints.up('sm')]: {
            marginLeft: '15px'
        },

        [theme.breakpoints.down('sm')]: {
            marginTop: '15px'
        }
    }
});