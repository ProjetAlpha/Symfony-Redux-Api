import React, { Component } from "react";
import { connect } from "react-redux";
import { MemoryRouter, Route } from 'react-router';
import { makeStyles, withStyles } from '@material-ui/core/styles';

import CustomDialog from './main/CustomDialog';

import * as UI from '../UI/Admin/base';
import Pagination from './main/Pagination';
import { getUsers, removeUserById } from '../actions/Admin';
import AdminStyle from '../UI/Admin/style';

class Admin extends React.Component {

    state = {
        triggerDialog: false,
        triggerSnack: false,
        user: {}
    }

    componentDidMount() {
        if (!this.props.users)
            this.props.getUsers();
    }

    UNSAFE_componentWillReceiveProps(nextProps) {
        if (nextProps.users !== this.props.users) {
            // console.log(nextProps.users);
        }
    }

    handleDelete(id) {
        this.props.removeUserById(id);
        this.handleDialog();
        this.setState({
            triggerSnack: true
        })
    }

    handleSnackBar() {
        this.setState(prevState => ({
            triggerSnack: !prevState.triggerSnack
        }))
    }

    handleDialog(user = {}) {
        this.setState({
            user: user
        })
        this.setState(prevState => ({
            triggerDialog: !prevState.triggerDialog
        }))
    }

    handleClose() {

    }

    render() {
        const classes = this.props.classes;
        return (
            <UI.Container component="main" maxWidth="xs">
                <div className={classes.root}>

                    <UI.List className={classes.root}>
                        {this.props.users && <Pagination baseUrl={'/admin'} maxItem={10} data={this.props.users} render={
                            (user, index) => (
                                <div key={index}>
                                    <UI.ListItem key={index}>
                                        <UI.ListItemText
                                            primary={user.email}
                                            secondary={
                                                user.lastname && user.firstname && <React.Fragment key={index}>
                                                    <UI.Typography
                                                        component="span"
                                                        variant="body2"
                                                        className={classes.inline}
                                                        color="textSecondary"
                                                    >
                                                        {user.lastname}, {user.firstname}
                                                    </UI.Typography>
                                                </React.Fragment>
                                            }
                                        />
                                        <UI.DeleteIcon className={classes.redIcon} onClick={this.handleDialog.bind(this, user)}></UI.DeleteIcon>
                                    </UI.ListItem>
                                    <UI.Divider component="li" />
                                </div>
                            )
                        }
                        />
                        }
                    </UI.List>
                    {
                        this.state.user && this.state.user.id && <CustomDialog open={this.state.triggerDialog ? true : false}
                            onConfirmation={this.handleDelete.bind(this, this.state.user.id)}
                            onClose={this.handleDialog.bind(this)}
                            text={this.state.user.firstname}
                        />
                    }
                    <UI.Snackbar open={this.state.triggerSnack}
                        autoHideDuration={5000}
                        onClose={this.handleSnackBar.bind(this)}
                        anchorOrigin={{ vertical: 'bottom', horizontal: 'right' }}
                    >
                    <React.Fragment>
                       {
                            !this.props.error &&
                            <UI.MuiAlert elevation={6} variant="filled" onClose={this.handleSnackBar.bind(this)} severity="success" >
                                Successfully remove a user !
                            </UI.MuiAlert>
                       }
                       {
                           this.props.error && 
                           <UI.MuiAlert elevation={6} variant="filled" onClose={this.handleSnackBar.bind(this)} severity="error" >
                                Remove user error !
                           </UI.MuiAlert>
                       }
                       </React.Fragment>
                    </UI.Snackbar>
                </div>
            </UI.Container>
        );
    }
}

const mapStateToProps = state => {
    return {
        error: state.Error.error,
        user: state.Authentification.user,
        users: state.Admin.users
    };
};

const adminStyle = withStyles(AdminStyle)(Admin);

export default connect(mapStateToProps, { getUsers, removeUserById })(adminStyle);