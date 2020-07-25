import useMediaQuery from '@material-ui/core/useMediaQuery';

export default theme => ({
    fullScreen: {
        [theme.breakpoints.down('sm')]: {
            width: '100%',
            height: '100%'
        }
    },
})