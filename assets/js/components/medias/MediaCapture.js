import React, { Component, Fragment } from 'react';

import { withStyles } from '@material-ui/core/styles';
import IconButton from '@material-ui/core/IconButton';
import PhotoCamera from '@material-ui/icons/PhotoCamera';
import DeleteIcon from '@material-ui/icons/Delete';
import Grid from '@material-ui/core/Grid';
import red from '@material-ui/core/colors/red';
import PropTypes from 'prop-types';

const styles = (theme) => ({
    input: {
        display: 'none'
    },
    image: {
        width: '150px',
        height: 'auto',
        margin: '12px'
    },
    capture: {
        display: 'flex'
    },
    icon: {
        alignSelf: 'flex-end'
    },
    button: {
        padding: '0!important'
    },
    imgContainer: {
        position: 'relative',
    },
    btnDelete: {
        position: 'absolute',
        color: red[500],
        cursor: 'pointer',
        bottom: '20px',
        left: '15px'
    }
});

class MediaCapture extends React.Component {

    state = {
        images: [],
        videos: []
    }

    componentDidUpdate(prevProps, prevState) {
        if (this.state.images !== prevState.images || this.state.videos !== prevState.videos) {
            this.props.onMediaUpdate(this.state);
        }
    }

    getFileInfo(filename, value) {
        const lastDot = filename.lastIndexOf('.');
        let name = filename.slice(0, lastDot);
        const ext = filename.slice(lastDot + 1);
        
        const parts = name.split('/');
        if (parts.length > 0) {
            name = parts.pop();
        }
        
        return {
            name: name,
            value: value,
            ext: ext
        };
    }

    handleFile(name, file) {
        const fileReader = new FileReader();

        fileReader.readAsDataURL(file);
        fileReader.onload = (e) => {
            if (this.state.images.length == this.props.maxCapture && this.props.maxCapture > 0) {
                const capture = this.state.images.map((value, index) => {
                    return index == this.props.maxCapture - 1 ? this.getFileInfo(file.name, e.target.result) : value;
                });
                this.setState({
                    [name]: capture
                })
            } else {
                this.setState((prevState) => ({
                    [name]: [...prevState[name], this.getFileInfo(file.name, e.target.result)]
                }));
            }
        };
    }

    handleDelete(name, targetIndex) {
        this.setState({
            [name]: this.state[name].filter((value, index) => index !== targetIndex)
        });
    }

    handleCapture = ({ target }) => {
        const name = target.accept.includes('image') ? 'images' : 'videos';
        const file = target.files[0];

        if (this.props.multiple) {
            for (let i = 0; i < target.files.length; i++) {
                this.handleFile(name, target.files[i]);
            }
        } else {
            this.handleFile(name, file);
        }
    }

    render() {
        const { classes } = this.props;

        return (
            <Fragment>
                {
                    this.state.images.map((image, index) => (
                        <div className={classes.imgContainer} key={index}>
                            <img src={image.value} className={classes.image} alt={image.name}/>
                            <DeleteIcon className={classes.btnDelete} onClick={this.handleDelete.bind(this, 'images', index)}></DeleteIcon>
                        </div>
                    ))
                }
                <div className={classes.capture}>
                    <input
                        accept="image/*"
                        className={classes.input}
                        id="icon-button-photo"
                        onChange={this.handleCapture}
                        type="file"
                        multiple={this.props.multiple}
                    />
                    <label htmlFor="icon-button-photo" className={classes.icon}>
                        <IconButton color="primary" component="span" className={classes.button}>
                            <PhotoCamera />
                        </IconButton>
                    </label>
                </div>

                {
                    /*
                        <input
                        accept="video/*"
                        capture="camcorder"
                        className={classes.input}
                        id="icon-button-video"
                        onChange={this.handleCapture}
                        type="file"
                        />
                        <label htmlFor="icon-button-video">
                            <IconButton color="primary" component="span">
                            <Videocam />
                            </IconButton>
                        </label>*/
                }
            </Fragment>
        );
    }
}

MediaCapture.propTypes = {
    preview: PropTypes.bool,
    multiple: PropTypes.bool,
    maxCapture: PropTypes.number,
    onMediaUpdate: PropTypes.func.isRequired
}

export default withStyles(styles, { withTheme: true })(MediaCapture);