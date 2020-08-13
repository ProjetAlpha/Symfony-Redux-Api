export default theme => ({
    flex: {
        margin: '30px',
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
        textAlign:'center'
    }
});