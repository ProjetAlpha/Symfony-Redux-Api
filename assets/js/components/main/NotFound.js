import Button from '@material-ui/core/Button';
import Typography from '@material-ui/core/Typography';
import Container from '@material-ui/core/Container';
import React, { Component } from "react";
import { withStyles } from '@material-ui/core/styles';
import { withRouter } from 'react-router-dom';
import { Link } from 'react-router-dom';

const classes = theme => ({
    root: {
        height: '100%',
        display: 'flex',
        alignItems:'center',
        justifyContent:'center',
        flexDirection:'column',
        padding: theme.spacing(0, 1)
    },
    toolbar: theme.mixins.toolbar,
    links: {
        flexDirection:'row',
        justifyContent:'space-between',
        maxWidth: '50%'
    },
});

class NotFound extends React.Component {
    
    render() {
        const classes = this.props.classes;
        return (
                <div className={classes.root}>
                    <div className={classes.toolbar} />
                        <Typography variant="h6">404 NOT FOUND</Typography>
                        <div className={classes.links}>
                            <Link to='/'> Home </Link>
                            <Link to='#' onClick={ () => this.props.history.goBack() }>
                                Back
                            </Link>
                        </div>
                </div>
        );
    }
}

const notFoundStyle = withStyles(classes)(NotFound);

export default withRouter(notFoundStyle);