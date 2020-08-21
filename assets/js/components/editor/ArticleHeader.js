import React, { Component } from 'react';
import * as UI from '../../UI/Editor/base';
import { connect } from "react-redux";
import MediaCapture from "../medias/MediaCapture";
import EditorStyle from '../../UI/Editor/style';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import { fetchArticle, updateArticle, createArticle } from '../../actions/Admin';

class ArticleHeader extends React.Component {

    state = {
        description: '',
        title: '',
        imageId: null
    }

    UNSAFE_componentWillReceiveProps(nextProps) {
        if (nextProps.article !== this.props.article) {
            this.setState({
                description: nextProps.article.description,
                title: nextProps.article.title,
                imageId: nextProps.article.cover_id
            });
            this.props.onInfoUpdate('description', nextProps.article.description);
            this.props.onInfoUpdate('title', nextProps.article.title);
        }
    }

    handleMedia(media) {
        const image = media.images[0];
        
        this.setState({
            image: image
        })
        this.props.onImageUpdate(image);
    }

    handleChange = name => event => {
        this.setState({ [name]: event.target.value });
        this.props.onInfoUpdate(name, event.target.value);
    }

    render() {
        const classes = this.props.classes;

        return (
            <UI.List className={classes.headerItems}>
                <div className={classes.header}>
                    <UI.Grid container spacing={2}>
                        <UI.Grid item xs={12} sm container>
                            <UI.Grid item xs container direction="column" spacing={2}>
                                <UI.Grid item xs>
                                    <UI.TextField
                                        id="standard-multiline-flexible"
                                        label="Title"
                                        multiline
                                        fullWidth
                                        rowsMax={6}
                                        value={this.state.title}
                                        InputProps={{
                                            endAdornment: (
                                              <UI.InputAdornment position="end">
                                                <UI.CreateIcon />
                                              </UI.InputAdornment>
                                            ),
                                        }}
                                        onChange={this.handleChange('title').bind(this)}
                                    />
                                </UI.Grid>
                                <UI.Grid item xs>
                                    <UI.TextField
                                        id="standard-multiline-flexible"
                                        label="Description"
                                        multiline
                                        fullWidth
                                        rowsMax={6}
                                        value={this.state.description}
                                        InputProps={{
                                            endAdornment: (
                                              <UI.InputAdornment position="end">
                                                <UI.CreateIcon />
                                              </UI.InputAdornment>
                                            ),
                                        }}
                                        onChange={this.handleChange('description').bind(this)}
                                    />
                                </UI.Grid>
                            </UI.Grid>
                        </UI.Grid>
                        <UI.Grid container item xs spacing={2}>
                            <MediaCapture preview={true}
                                maxCapture={1} multiple={false}
                                onMediaUpdate={this.handleMedia.bind(this)}
                                imageId={this.state.imageId}
                            />
                        </UI.Grid>
                    </UI.Grid>
                </div>
            </UI.List>
        );
    }
}

const mapStateToProps = state => {
    return {
        error: state.Error.error,
        user: state.Authentification.user,
        users: state.Admin.users,
        success: state.Success.success,
        article: state.Admin.article
    };
};

const editorStyle = withStyles(EditorStyle)(ArticleHeader);

ArticleHeader.propTypes = {
    onImageUpdate: PropTypes.func.isRequired,
    onInfoUpdate: PropTypes.func.isRequired,
    description: PropTypes.string,
    title: PropTypes.string,
    image: PropTypes.string
}

export default connect(mapStateToProps, { fetchArticle, updateArticle, createArticle })(editorStyle);
