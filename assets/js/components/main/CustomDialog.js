import React, { Component } from "react";
import { Link } from 'react-router-dom';

import * as UI from '../../UI/Dialog/base';

import { makeStyles, withStyles, withTheme } from '@material-ui/core/styles';
import DialogStyle from '../../UI/Dialog/style';

const CLOSE = 0;
const CONFIRMATION = 1;

class CustomDialog extends React.Component {

    state = {
        openDialog: false
    }

    handleClick(type) {
        switch (type) {
            case CLOSE:
                this.props.onClose();
            break;
            case CONFIRMATION:
                this.props.onConfirmation();
            default:
                return ;
        }
    }

    render() {
        const classes = this.props.classes;
        // fullScreen={classes.fullScreen}
        return (
            <div>
                <UI.Dialog
                    open={this.props.open}
                    onClose={this.handleClick.bind(this, CLOSE)}
                    aria-labelledby="responsive-dialog-title"
                >
                    <UI.DialogTitle id="responsive-dialog-title">{"Delete this user ?"}</UI.DialogTitle>
                    <UI.DialogContent>
                        <UI.DialogContentText>
                            Are you sure to delete this user ? - { this.props.text }
                        </UI.DialogContentText>
                    </UI.DialogContent>
                    <UI.DialogActions>
                        <UI.Button autoFocus onClick={this.handleClick.bind(this, CONFIRMATION)} color="primary">
                            Confirmation
                        </UI.Button>
                        <UI.Button onClick={this.handleClick.bind(this, CLOSE)} color="primary" autoFocus>
                            Cancel
                        </UI.Button>
                    </UI.DialogActions>
                </UI.Dialog>
            </div>
        );
    }
}

export default withStyles(DialogStyle)(CustomDialog);