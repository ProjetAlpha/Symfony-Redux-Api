import React, { Component } from "react";
import { connect } from "react-redux";
import { makeStyles, withStyles } from '@material-ui/core/styles';

import * as UI from '../UI/Admin/base';

import { getUsers, removeUserById } from '../actions/Admin';
import AdminStyle from '../UI/Admin/style';

class Admin extends React.Component {
    state = {
        
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

    render() {
        const classes = this.props.classes;

        return (
            <div className={classes.root}>
            { this.props.users &&
                this.props.users.map((user, index) => (
                <UI.Card className={classes.card} key={index}>
                    <div className={classes.details}>
                        <UI.CardContent className={classes.content}>
                            <UI.Typography component="h5" variant="h5">
                                { user.email }
                            </UI.Typography>
                            <UI.Typography variant="subtitle1" color="textSecondary">
                                { user.firstname } { user.lastname }
                            </UI.Typography>
                        </UI.CardContent>
                    </div>
                </UI.Card>
                ))
            }
            </div>
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