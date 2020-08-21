import { FormHelperText } from "./Admin/base";

export const pad = {

    x_05: {
        paddingLeft: '0.5rem',
        paddingRight: '0.5rem'
    },

    x_1: {
        paddingLeft: '1rem',
        paddingRight: '1rem'
    }
};

export const mr = {
    
    x_05: {
        marginLeft: '0.5rem',
        marginRight: '0.5rem'
    },

    x_1: {
        marginLeft: '1rem',
        marginRight: '1rem'
    }
};

export const colors = {
    green: '#53e07e',
    lightBlue:'#00e2fa',
    lightBlue2:'#1976d2',
    blue: '#52A0FD'
}

export const buttons = {
    green: theme => ({
        [theme.breakpoints.down('sm')]: {
          fontSize:'11px',
          margin:theme.spacing(0.5)
        },
        marginRight: theme.spacing(2),
        color: '#ffffff',
        borderRadius: 3,
        border: 0,
        fontFamily:"'Montserrat', Helvetica, Arial, sans-serif",
        backgroundImage: 'linear-gradient(to right, #53e07e 100%, #39fad7 0%, #39fad7 0%)'
    }),
    blue: theme => ({
        [theme.breakpoints.down('sm')]: {
          fontSize:'11px',
          margin:theme.spacing(0.5)
        },
        backgroundImage:'linear-gradient(to right, #52A0FD 100%, #00e2fa 0%, #00e2fa 0%)',
        marginRight: theme.spacing(2),
        fontFamily:"'Montserrat', Helvetica, Arial, sans-serif",
        color: '#ffffff',
        borderRadius: 3,
        border: 0,
    })
}

export const body = theme => ({
    root: {
        display:'flex',
        flexDirection: 'column',
        minHeight: '100%'
    },
    toolbar: theme.mixins.toolbar,
})