import React, { Component } from "react";
import { Link } from 'react-router-dom';
import { connect } from "react-redux";

import * as UI from '../../UI/SnackBar/base';

import { withStyles } from '@material-ui/core/styles';
import SnackBarStyle from '../../UI/SnackBar/style';

class CustomSnackBar extends React.Component {
    state = {

    }

    render() {
        return (
            <UI.Snackbar open={this.props.open}
                autoHideDuration={this.props.time}
                onClose={this.props.onClose}
                anchorOrigin={this.props.position}
            >
                { /* Fix material-ui bug with react fragement */ }
                <>
                    {
                        !this.props.error &&
                        <UI.MuiAlert elevation={6} variant="filled" onClose={this.props.onClose} severity="success">
                            { this.props.message.success }
                        </UI.MuiAlert>
                    }
                    {
                        this.props.error &&
                        <UI.MuiAlert elevation={6} variant="filled" onClose={this.props.onClose} severity="error">
                            { this.props.message.error }
                        </UI.MuiAlert>
                    }
                </>
            </UI.Snackbar>
        );
    }
}

const mapStateToProps = state => {
    return {
        error: state.Error.error
    };
};

const snackStyle = withStyles(SnackBarStyle)(CustomSnackBar);

export default connect(mapStateToProps)(snackStyle);