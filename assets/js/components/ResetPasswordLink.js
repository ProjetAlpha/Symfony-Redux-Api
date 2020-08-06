import React, { Component } from "react";
import { connect } from "react-redux";
import { withStyles } from '@material-ui/core/styles';
import { withRouter } from 'react-router-dom';

import * as UI from '../UI/Login/base';
import { Link } from 'react-router-dom';
import LoginStyle from '../UI/Login/style';
import { login } from '../actions/Authentification';
import { reset } from '../actions/Success';
import { clearError } from '../actions/Error';
import { getError } from "../utils/Error";
import { confirmPasswordReset } from '../actions/Settings';
import { getBodyHeight } from '../utils/Responsive';
import { setHelperText } from '../utils/Validation';

class ResetPasswordLink extends React.Component {

    state = {
        password: null
    }

    UNSAFE_componentWillReceiveProps(nextProps) {
        if (nextProps.success !== this.props.success) {
            if (nextProps.success) {
                this.props.history.push('/');

                // clear error and reset register success.
                this.props.reset();
                this.props.clearError();
            } else {
                this.props.history.push('/resetPassword');
            }
        }
    }

    handleSubmit(event) {
        event.preventDefault();

        if (!this.state.password) return;

        this.props.confirmPasswordReset(this.props.match.params.id, this.state.password);
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
                                        <UI.LockOutlinedIcon />
                                    </UI.Avatar>
                                </UI.Grid>
                                <UI.Grid item xs={12}>
                                    <UI.Typography component="h1" variant="h5">
                                        Reset your password
                                    </UI.Typography>
                                </UI.Grid>
                                <UI.TextField
                                    variant="outlined"
                                    margin="normal"
                                    required
                                    fullWidth
                                    id="password"
                                    label="New password"
                                    name="password"
                                    autoComplete="password"
                                    type="password"
                                    autoFocus
                                    onChange={this.handleChange('password').bind(this)}
                                    error={getError(this.props, 'password') ? true : false}
                                    helperText={setHelperText(this.props)}
                                />
                                <UI.Button
                                    type="submit"
                                    fullWidth
                                    variant="contained"
                                    color="primary"
                                    className={classes.submit}
                                    onClick={this.handleSubmit.bind(this)}
                                >
                                    CONFIRM
                                </UI.Button>
                            </UI.Grid>
                        </UI.Grid>
                    </form>
                </div>
            </UI.Container>
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

const resetStyle = withStyles(LoginStyle)(ResetPasswordLink);

const router = withRouter(resetStyle);

export default connect(mapStateToProps, { login, reset, clearError, confirmPasswordReset })(router);