import React, { Component } from 'react';
import * as UI from '../../UI/Editor/base';
import { connect } from "react-redux";
import MediaCapture from "../medias/MediaCapture";
import EditorStyle from '../../UI/Editor/style';
import PropTypes from 'prop-types';
import { makeStyles, withStyles } from '@material-ui/core/styles';
import { fetchArticle, updateArticle, createArticle } from '../../actions/Admin';

// post title, description and image
class ArticleHeader extends React.Component {

    state = {
        description: '',
        title: '',
        image: ''
    }

    componentDidMount() {
        if (this.props.description) {
            this.setState({
                description: this.props.description
            })
        }

        if (this.props.title) {
            this.setState({
                title: this.props.title
            })
        }

        if (this.props.image) {
            this.setState({
                image: this.props.image
            })
        }
    }

    UNSAFE_componentWillReceiveProps(nextProps) {
        // editor component has been updated - post title, description, image
        if (nextProps.articles !== this.props.articles) {

            // this.props.updateArticle(this.props.user.id, articleId || this.props.articles.id, {
            // raw_data: rawData,
            // is_draft: !isDraft ? true : false,
            // description,
            // title
            // });
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
        // create / update : article editor updates => new props => update article with title & description
        // image is automatically upload or replace if exist
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
                                        onChange={this.handleChange('description').bind(this)}
                                    />
                                </UI.Grid>
                            </UI.Grid>
                        </UI.Grid>
                        <UI.Grid container item xs spacing={2}>
                            <MediaCapture preview={true} maxCapture={1} multiple={false} onMediaUpdate={this.handleMedia.bind(this)}/>
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
        articles: state.Admin.articles
    };
};

const editorStyle = withStyles(EditorStyle)(ArticleHeader);

// title, description, image
ArticleHeader.propTypes = {
    onImageUpdate: PropTypes.func.isRequired,
    onInfoUpdate: PropTypes.func.isRequired,
    description: PropTypes.string,
    title: PropTypes.string,
    image: PropTypes.string
}

export default connect(mapStateToProps, { fetchArticle, updateArticle, createArticle })(editorStyle);
