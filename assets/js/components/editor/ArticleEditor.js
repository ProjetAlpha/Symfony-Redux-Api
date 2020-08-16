import React, { Component } from 'react';
import * as UI from '../../UI/Editor/base';
import { connect } from "react-redux";
import { Modifier, convertToRaw, EditorState, AtomicBlockUtils, RichUtils, getDefaultKeyBinding, convertFromRaw,KeyBindingUtil } from "draft-js";
import sanitizeHtml from 'sanitize-html';

import draftToHtml from 'draftjs-to-html';
import { getBlocksWhereEntityData } from './utils';
import EditorStyle from '../../UI/Editor/style';
import { makeStyles, withStyles } from '@material-ui/core/styles';
import { fetchArticle, updateArticle, createArticle } from '../../actions/Admin';
import { reset } from '../../actions/Success';
import { clearError } from '../../actions/Error';
import { Editor } from 'react-draft-wysiwyg';

class ArticleEditor extends React.Component {

  state = {
    editorState: EditorState.createEmpty(),
    images: {},
    updated: false,
    needUpdate: false,
    timeouts: [],
    currentTextAlignment: null
  };

  UNSAFE_componentWillReceiveProps(nextProps) {
    if (nextProps.success !== this.props.success) {
        if (nextProps.success) {
            // clear error and reset success.
            this.props.reset();
            this.props.clearError();
            this.setState({ updated:false });
        } else {
            // error handler
            // this.props.history.push('/resetPassword');
        }
    }
  }

  editorIsEmpty(contentState) {
    return !(contentState.hasText() && (contentState.getPlainText() !== '') && contentState !== EditorState.createEmpty());
  }

  updateArticle() {
    const contentState = this.state.editorState.getCurrentContent();
    const { articleId, isDraft } = this.props.match.params;

    if (!this.state.needUpdate) return ;

    this.setState({ updated:true, needUpdate: false });
    const rawData = draftToHtml(convertToRaw(contentState));

      if (articleId || (this.props.articles && this.props.articles.id)) {
          this.props.updateArticle(this.props.user.id, articleId || this.props.articles.id, {
            raw_data: rawData,
            is_draft: !isDraft ? true : false
          });
      } else {
          this.props.createArticle(this.props.user.id, {
            raw_data: rawData,
            is_draft: true
          });
      }
  }

  onChange = (editorState) => {
    const currentContent = this.state.editorState.getCurrentContent();
    const newContent = editorState.getCurrentContent();

    if (currentContent !== newContent && !this.editorIsEmpty(currentContent)) {
      this.setState({
        needUpdate: true
      })
    }

    this.setState({
      editorState: editorState
    });
  };

  focus = () => {
    this.editor.focus();
  };

  componentDidMount() {
    const { articleId, isDraft } = this.props.match.params;
    
    if (articleId) {
      this.props.fetchArticle(this.props.user.id, articleId);
      // will receive props
      // EDIT : USE html to draft - render blocks.
      // this.setState({ editorState: EditorState.createWithContent(convertFromRaw(this.articles.raw_data)) })
    }

    // const selection = EditorState.getSelection();
    // textAlignment 'left', 'center', and 'right'.
      this.setState({ timeouts: [...this.state.timeouts,
          setInterval(this.updateArticle.bind(this), 2000),
          /*setTimeout(this.setResponsiveImage.bind(this, newContent), 750)*/
        ]
      });

    /*this.setState({
      timeout: setInterval(this.updateArticle.bind(this), 3000)
    });*/
    
    document.addEventListener("keydown", this.onKeyPressed.bind(this));
  }

  componentWillUnmount() {
    this.state.timeouts.forEach(timeout => clearInterval(timeout));
    document.removeEventListener("keydown", this.onKeyPressed.bind(this));
  }

  onKeyPressed(e){
    if (event.keyCode === 9) {
      event.preventDefault();
    }
  }

  uploadImage = (file) => {
    console.log(file);
    return new Promise(
      (resolve, reject) => {
        // upload base 64 image & send path
        resolve('test');
    })
  }

  render() {
    const classes = this.props.classes;

    // search by title
    // title
    // description
    // image
    // text editor
    return (
      <UI.Container>
      <div className={classes.flex}>
        <div>
          <Editor
            editorState={this.state.editorState}
            toolbarClassName="rdw-storybook-toolbar"
            wrapperClassName="rdw-storybook-wrapper"
            editorClassName="rdw-storybook-editor"
            onEditorStateChange={this.onChange}
            toolbar={{ image: { uploadCallback: this.uploadImage, alt: { present: true, mandatory: true } } }}
          />
        </div>
        <div className={classes.btnCenter}>
          <UI.Button variant="contained" color="primary" component="span">
                Publish
          </UI.Button>
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
      articles: state.Admin.articles
  };
};

const editorStyle = withStyles(EditorStyle)(ArticleEditor);

export default connect(mapStateToProps, { fetchArticle, updateArticle, createArticle, reset, clearError })(editorStyle);

