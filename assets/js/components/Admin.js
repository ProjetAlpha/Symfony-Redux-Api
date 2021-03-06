import React, { Component } from "react";
import { connect } from "react-redux";
import { MemoryRouter, Route } from 'react-router';
import { makeStyles, withStyles } from '@material-ui/core/styles';

import CustomDialog from './main/CustomDialog';

import * as UI from '../UI/Admin/base';
import Pagination from './main/Pagination';
import CustomSnackBar from './main/CustomSnackBar';
import { getUsers, removeUserById } from '../actions/Admin';
import Search from './main/Search';

import AdminStyle from '../UI/Admin/style';

// ****** TODO : handle custom dialog and snackbar with redux states ******
class Admin extends React.Component {

    state = {
        triggerDialog: false,
        triggerSnack: false,
        user: {},
        users: [],
        loading: true
    }

    componentDidMount() {
        if (!this.props.users)
            this.props.getUsers();
        else {
            this.setState({
                users: this.props.users,
                loading: false
            })
        }
    }

    UNSAFE_componentWillReceiveProps(nextProps) {
        if (nextProps.users !== this.props.users) {
            this.setState({
                users: nextProps.users,
                loading: false
            })
        }
    }

    handleDelete(id) {
        this.props.removeUserById(id);
        this.handleDialog();
        this.setState({
            triggerSnack: true
        })
    }

    handleDialog(user = {}) {
        this.setState({
            user: user
        })
        this.setState(prevState => ({
            triggerDialog: !prevState.triggerDialog
        }))
    }

    handleSnackBar() {
        this.setState(prevState => ({
            triggerSnack: !prevState.triggerSnack
        }))
    }

    update(users) {
        this.setState({
            users: users
        })
    }

    render() {
        const classes = this.props.classes;
        return (
            <UI.Container>
                <div className={classes.root}>
                    <UI.List className={classes.root}>
                            <UI.ListItem className={classes.searchItem}>
                                <Search
                                    filterData={(search, data) =>
                                    -1 !== data.lastname.toLowerCase().indexOf(search)
                                    || -1 !== data.firstname.toLowerCase().indexOf(search)
                                    || -1 !== data.email.toLowerCase().indexOf(search)}
                                    data={this.state.users || []}
                                    update={ (users) => this.update(users) }
                                    reset={ (users) => this.update(users) }
                                    isLoading={this.state.loading}
                                />
                            </UI.ListItem>
                        {this.state.users && <Pagination baseUrl={'/admin'} maxItem={10} data={this.state.users} render={
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
                        this.state.user && this.state.user.id &&
                        <CustomDialog open={this.state.triggerDialog ? true : false}
                            onConfirmation={this.handleDelete.bind(this, this.state.user.id)}
                            onClose={this.handleDialog.bind(this)}
                            text={this.state.user.firstname}
                        />
                    }
                    <CustomSnackBar
                        open={this.state.triggerSnack ? true : false}
                        time={5000}
                        position={{ vertical: 'bottom', horizontal: 'right' }}
                        message={{error: 'Remove user error !' , success: 'Successfully remove a user !'}}
                        onClose={this.handleSnackBar.bind(this)}
                    />
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