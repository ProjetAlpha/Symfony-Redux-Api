import React, { Component } from 'react';
import * as UI from '../../UI/Editor/base';
import { connect } from "react-redux";
import { convertToRaw, EditorState, RichUtils, convertFromRaw, KeyBindingUtil, ContentState } from "draft-js";
import sanitizeHtml from 'sanitize-html';

import CustomSnackBar from '../main/CustomSnackBar';
import draftToHtml from 'draftjs-to-html';
import htmlToDraft from 'html-to-draftjs';
import EditorStyle from '../../UI/Editor/style';
import { withStyles } from '@material-ui/core/styles';
import { fetchArticle, updateArticle, createArticle } from '../../actions/Admin';
import { uploadImage } from '../../actions/Image';
import { reset } from '../../actions/Success';
import { clearError } from '../../actions/Error';
import { Editor } from 'react-draft-wysiwyg';
import ArticleHeader from './ArticleHeader';

class ArticleEditor extends React.Component {

  state = {
    editorState: EditorState.createEmpty(),
    images: {},
    isEditable: false,
    textUpdate: false,
    imageUpdate: false,
    image: {},
    title: '',
    description: '',
    isDraft: false,
    articleId: null,
    triggerDialog: false,
    feedback: false
  };

  // remove new line on enter (make no sense, people are used to microsft word)
  handleReturn = (event, editorState) => {
    const isSoftNewLineEvent = !KeyBindingUtil.isSoftNewlineEvent(event);
    if (isSoftNewLineEvent) {
      const newEditorState = RichUtils.insertSoftNewline(editorState);
      if (newEditorState) {
        this.onChange(RichUtils.insertSoftNewline(editorState));
      }
      return 'handled';
    }

    return 'not-handled';
  }

  UNSAFE_componentWillReceiveProps(nextProps) {
    if (nextProps.article && nextProps.article !== this.props.article) {
      if (this.state.isEditable) {
        this.loadArticleHtml(nextProps.article);
        this.setState({
          isEditable: false
        })
      }

      this.setState({
        articleId: nextProps.article.id
      })

      if (this.state.feedback) {
        this.setState({
          triggerSnack: true,
          feedback: false
        })
      }

      if (this.state.imageUpdate) {
        this.props.uploadImage(this.props.user, {
          email: this.props.user.email,
          name: this.state.image.name,
          base64_image: this.state.image.value,
          extension: this.state.image.ext,
          is_article_cover: true,
          extra_id: nextProps.article.id
        });
        this.setState({
          imageUpdate: false
        })
      }
    }

    if (nextProps.success !== this.props.success) {
      if (nextProps.success) {
        // clear error and reset success.
        this.props.reset();
        this.props.clearError();
      } else {
        // error handler here
      }
    }
  }

  editorIsEmpty(contentState) {
    return !(contentState.hasText() && (contentState.getPlainText() !== '') && contentState !== EditorState.createEmpty());
  }

  updateArticle(isDraft = false, feedback = false) {
    if (feedback) {
      this.setState({
        feedback: true
      })
    }

    const contentState = this.state.editorState.getCurrentContent();
    if (!this.state.textUpdate && !this.state.imageUpdate && !feedback) return;

    this.setState({ textUpdate: false });
    const rawData = draftToHtml(convertToRaw(contentState));
    const id = this.state.articleId;

    if (id) {
      this.props.updateArticle(this.props.user.id, id, {
        raw_data: rawData,
        is_draft: isDraft,
        title: this.state.title,
        description: this.state.description
      });
    } else {
      this.props.createArticle(this.props.user.id, {
        raw_data: rawData,
        is_draft: isDraft,
        title: this.state.title,
        description: this.state.description
      });
    }
  }

  onChange = (editorState) => {
    const currentContent = this.state.editorState.getCurrentContent();
    const newContent = editorState.getCurrentContent();

    if (currentContent !== newContent && !this.editorIsEmpty(currentContent)) {
      this.setState({
        textUpdate: true
      })
    }

    this.setState({
      editorState: editorState
    });
  };

  loadArticleHtml(article) {
    const blocksFromHtml = htmlToDraft(article.raw_data);
    const { contentBlocks, entityMap } = blocksFromHtml;
    const contentState = ContentState.createFromBlockArray(contentBlocks, entityMap);
    const editorState = EditorState.createWithContent(contentState);

    this.setState({
      editorState: editorState
    });
  }

  componentDidMount() {
    const { articleId } = this.props.match.params;

    if (articleId) {
      this.setState({
        articleId: articleId,
        isEditable: true
      });

      this.props.fetchArticle(this.props.user.id, articleId);
    }
  }

  handleHeaderImage(image) {
    this.setState({
      image: image,
      imageUpdate: true
    });
  }

  handleHeaderInfo(name, value) {
    this.setState({
      [name]: value,
      textUpdate: true
    })
  }

  handleSnackBar() {
    this.setState(prevState => ({
      triggerSnack: !prevState.triggerSnack
    }))
  }

  uploadImage = (file) => {
    console.log(file);
    return new Promise(
      (resolve, reject) => {
        // upload base 64 image & send path e.g. /api/image/fetch/{id}
        resolve('test');
      })
  }

  render() {
    const classes = this.props.classes;

    return (
      <UI.Container>
        <div className={classes.flex}>
          <ArticleHeader onImageUpdate={this.handleHeaderImage.bind(this)} onInfoUpdate={this.handleHeaderInfo.bind(this)} />
          <div>
            <Editor
              handleReturn={this.handleReturn}
              editorState={this.state.editorState}
              toolbarClassName="rdw-storybook-toolbar"
              wrapperClassName="rdw-storybook-wrapper"
              editorClassName="rdw-storybook-editor"
              onEditorStateChange={this.onChange}
              toolbar={{ image: { uploadCallback: this.uploadImage, alt: { present: true, mandatory: true } } }}
            />
          </div>
          <div className={classes.btnCenter}>
            <UI.Button variant="contained" variant="outlined" color="primary" component="span" onClick={this.updateArticle.bind(this, false, true)}>
              Publish article
          </UI.Button>
            <UI.Button className={classes.mr_l_t_15_mobile} variant="contained" variant="outlined" color="secondary" onClick={this.updateArticle.bind(this, true, true)}>
              Save to drafts
          </UI.Button>
            <CustomSnackBar
              open={this.state.triggerSnack ? true : false}
              time={3500}
              position={{ vertical: 'bottom', horizontal: 'right' }}
              message={{ error: 'An error occured during article upload', success: 'Successfully saved article' }}
              onClose={this.handleSnackBar.bind(this)}
            />
          </div>
        </div>
      </UI.Container>
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

const editorStyle = withStyles(EditorStyle)(ArticleEditor);

export default connect(mapStateToProps, { fetchArticle, updateArticle, uploadImage, createArticle, reset, clearError })(editorStyle);