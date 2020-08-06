import React, { Component } from "react";
import { connect } from "react-redux";
import { makeStyles, withStyles } from '@material-ui/core/styles';

import * as UI from '../UI/Login/base';
import LoginStyle from '../UI/Login/style';
import { login, resetSuccess } from '../actions/Authentification';
import { clearError } from '../actions/Error';
import { getError } from "../utils/Error";
import { sendResetPasswordLink } from '../actions/Settings';
import { getBodyHeight } from '../utils/Responsive';
import { setHelperText } from '../utils/Validation';

class ResetPassword extends React.Component {

    state = {
        email: null
    }

    handleSubmit(event) {
        event.preventDefault();

        if (!this.state.email) return;

        this.props.sendResetPasswordLink(this.state.email);
    }

    handleChange = name => event => {
        this.setState({ [name]: event.target.value });
    }

    render() {
        const classes = this.props.classes;

        return (
            <UI.Container component="main" maxWidth="xs">
                <UI.CssBaseline />
                <div className={classes.center}>
                    <form className={classes.form} action="#" onSubmit={this.handleSubmit.bind(this)}>
                        <UI.Grid container spacing={2} direction="column"
                            alignItems="center"
                            justify="center"
                            style={{ minHeight: getBodyHeight() }}>
                            <UI.Grid item xs={12}>
                                <UI.Grid item xs={12} align="center">
                                    <UI.Avatar className={classes.avatar}>
                                        <UI.MailIcon />
                                    </UI.Avatar>
                                </UI.Grid>
                                <UI.Grid item xs={12}>
                                    <UI.Typography component="h1" variant="h5">
                                        Send an email to reset your password
                                </UI.Typography>
                                </UI.Grid>
                                <UI.TextField
                                    variant="outlined"
                                    margin="normal"
                                    required
                                    fullWidth
                                    id="email"
                                    label="Email Address"
                                    name="email"
                                    autoComplete="email"
                                    autoFocus
                                    onChange={this.handleChange('email').bind(this)}
                                    error={getError(this.props, 'email') ? true : false}
                                    helperText={setHelperText(this.props, 'Email sent successfully. Verify your emails to reset your password.')}
                                />
                                <UI.Button
                                    type="submit"
                                    fullWidth
                                    variant="contained"
                                    color="primary"
                                    className={classes.submit}
                                    onClick={this.handleSubmit.bind(this)}
                                >
                                    SEND
                                </UI.Button>
                            </UI.Grid>
                        </UI.Grid>
                    </form>
                </div>
            </UI.Container >
        );
    }
}

const mapStateToProps = state => {
    return {
        error: state.Error.error,
        success: state.Success.success,
        user: state.Authentification.user
    };
};

const resetStyle = withStyles(LoginStyle)(ResetPassword);

export default connect(mapStateToProps, { login, resetSuccess, clearError, sendResetPasswordLink })(resetStyle);