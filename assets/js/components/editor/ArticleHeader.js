import React, { Component } from 'react';
import * as UI from '../../UI/Editor/base';
import { connect } from "react-redux";
import MediaCapture from "../medias/MediaCapture";
import EditorStyle from '../../UI/Editor/style';
import { fetchArticle, updateArticle, createArticle } from '../../actions/Admin';

// post title, description and image
class ArticleHeader extends React.Component {

    state = {
        description: '',
        title: ''
    }

    UNSAFE_componentWillReceiveProps(nextProps) {
        // editor component has been updated
        if (nextProps.articles !== this.props.articles) {
            // this.props.updateArticle(this.props.user.id, articleId || this.props.articles.id, {
            // raw_data: rawData,
            // is_draft: !isDraft ? true : false,
            // description,
            // title
            // });
        }
    }

    handleChange = name => event => {
        this.setState({ [name]: event.target.value });
        // create / update : article editor updates => new props => update article with title & description
        // image is automatically upload or replace if exist
    }

    render() {
        <div>
            <UI.Container>
                <TextField
                    id="standard-multiline-flexible"
                    label="Title"
                    multiline
                    rowsMax={4}
                    value={this.state.title}
                    onChange={this.handleChange('title').bind(this)}
                />
                <TextField
                    id="standard-multiline-flexible"
                    label="Description"
                    multiline
                    rowsMax={4}
                    value={this.state.description}
                    onChange={this.handleChange('description').bind(this)}
                />
                <MediaCapture />
            </UI.Container>
        </div>
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

export default connect(mapStateToProps, { fetchArticle, updateArticle, createArticle })(editorStyle);
