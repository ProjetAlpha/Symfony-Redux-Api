import React, { Component } from 'react';
import * as UI from '../../UI/Editor/base';
import { connect } from "react-redux";
import { convertToRaw, EditorState, AtomicBlockUtils, RichUtils, getDefaultKeyBinding, convertFromRaw } from "draft-js";
import Editor,  { composeDecorators } from 'draft-js-plugins-editor';
import createUndoPlugin from 'draft-js-undo-plugin';
import createVideoPlugin from 'draft-js-video-plugin';
import createHashtagPlugin from 'draft-js-hashtag-plugin';
import createLinkifyPlugin from 'draft-js-linkify-plugin';
import createAlignmentPlugin from 'draft-js-alignment-plugin';
import createResizeablePlugin from 'draft-js-resizeable-plugin';
import createFocusPlugin from 'draft-js-focus-plugin';
import createBlockDndPlugin from 'draft-js-drag-n-drop-plugin';
import createImagePlugin from 'draft-js-image-plugin';
import createEmojiPlugin from 'draft-js-emoji-plugin';
import createDragNDropUploadPlugin from 'draft-js-dragndrop-upload-plugin';
import createToolbarPlugin, { Separator } from 'draft-js-static-toolbar-plugin';
import { getBlocksWhereEntityData } from './utils';
import EditorStyle from '../../UI/Editor/style';
import { makeStyles, withStyles } from '@material-ui/core/styles';
import { fetchArticle, updateArticle, createArticle } from '../../actions/Admin';
import { reset } from '../../actions/Success';
import { clearError } from '../../actions/Error';

import {
  ItalicButton,
  BoldButton,
  UnderlineButton,
  CodeButton,
  HeadlineOneButton,
  HeadlineTwoButton,
  HeadlineThreeButton,
  UnorderedListButton,
  OrderedListButton,
  BlockquoteButton,
  CodeBlockButton,
  AlignBlockCenterButton,
  AlignBlockLeftButton,
  AlignBlockRightButton,
  AlignBlockDefaultButton
} from 'draft-js-buttons';

const undoPlugin = createUndoPlugin();
const { UndoButton, RedoButton } = undoPlugin;

const focusPlugin = createFocusPlugin();
const resizeablePlugin = createResizeablePlugin();
const blockDndPlugin = createBlockDndPlugin();
const alignmentPlugin = createAlignmentPlugin();
const { AlignmentTool } = alignmentPlugin;

const hashtagPlugin = createHashtagPlugin();
const linkifyPlugin = createLinkifyPlugin();

const staticToolbarPlugin = createToolbarPlugin();
const { Toolbar } = staticToolbarPlugin;

const decorator = composeDecorators(
  resizeablePlugin.decorator,
  alignmentPlugin.decorator,
  focusPlugin.decorator,
  blockDndPlugin.decorator
);

const emojiPlugin = createEmojiPlugin({ decorator });
const { EmojiSuggestions, EmojiSelect } = emojiPlugin;

const imagePlugin = createImagePlugin({ decorator });
const videoPlugin = createVideoPlugin({ decorator });

const dragNDropFileUploadPlugin = createDragNDropUploadPlugin({
  handleUpload: () => console.log('upload'), //write your image upload codes in upload.js
  addImage: imagePlugin.addImage,
});

const plugins = [
  emojiPlugin,
  undoPlugin,
  linkifyPlugin,
  hashtagPlugin,
  imagePlugin,
  videoPlugin,
  emojiPlugin,
  alignmentPlugin,
  resizeablePlugin,
  blockDndPlugin,
  dragNDropFileUploadPlugin,
  staticToolbarPlugin
];

class ArticleEditor extends React.Component {

  state = {
    editorState: EditorState.createEmpty(),
    prevState: null,
    images: {},
    updated: false,
    timeout: null
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

  updateArticle() {
    const contentState = this.state.editorState.getCurrentContent();
    const rawData = JSON.stringify(convertToRaw(contentState));
    const { articleId, isDraft } = this.props.match.params;

    if (contentState !== EditorState.createEmpty() && !this.state.updated) {
      
      this.setState({ updated:true });
      
      if (articleId || (this.props.articles && this.props.articles.id)) {
        console.log('update');
          this.props.updateArticle(this.props.user.id, articleId || this.props.articles.id, {
            raw_data: rawData,
            is_draft: !isDraft ? true : false
          });
      } else {
        console.log('create');
          this.props.createArticle(this.props.user.id, {
            raw_data: rawData,
            is_draft: true
          });
      }
    }
  }

  // save published or draft article
  onChange = (editorState) => {
    console.log('change');
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
      // this.setState({ editorState: EditorState.createWithContent(convertFromRaw(this.articles.raw_data)) })
    }
    
    this.setState({
      timeout: setInterval(this.updateArticle.bind(this), 3000)
    });
    
    document.addEventListener("keydown", this.onKeyPressed.bind(this));
  }

  componentWillUnmount() {
    clearInterval(this.state.timeout);
    document.removeEventListener("keydown", this.onKeyPressed.bind(this));
  }      

  handleEditorChange = editorState => this.setState({ editorState: editorState })

  onKeyPressed(e){
    if (event.keyCode === 9) {
      event.preventDefault();
    }
  }

  handleDropFiles = (selection, files) => {
    /*for (let i = 0; i < files.length; i++) {
      
      if (!files[i] instanceof Blob) continue;

      let reader = new FileReader();
      reader.readAsDataURL(files[i]);
      
      reader.onloadend = () => {
        const res = reader.result;
        const id = Date.now().toString(36) + Math.random().toString(36).substr(2);
        if (!this.state.image[id]) {
          this.setState({
            images: {...images, [id] : res}
          })
          // save images to server
        }
      };
    }*/
  }

  handlePastedFiles = (files) => {
    //console.log(files);
  }

  // TODO: fix tabulation.
  handleKeyBindings = e => {
    if (e.keyCode === 9) {
      const newEditorState = RichUtils.onTab(e, this.state.editorState, 6 /* maxDepth */);
      this.handleEditorChange(newEditorState);
      if (newEditorState !== this.state.editorState) {
         this.handleEditorChange(newEditorState)
      }
  
      return
    }
  
    return getDefaultKeyBinding(e);
  }

  render() {
    const classes = this.props.classes;

    return (
      <div className={classes.flex}>
        <div>
          <Toolbar>
          {
              // may be use React.Fragment instead of div to improve perfomance after React 16
              (externalProps) => (
                <>
                  <BoldButton {...externalProps} />
                  <ItalicButton {...externalProps} />
                  <UnderlineButton {...externalProps} />
                  <CodeButton {...externalProps} />
                  <UnorderedListButton {...externalProps} />
                  <OrderedListButton {...externalProps} />
                  <BlockquoteButton {...externalProps} />
                  <Separator {...externalProps} />
                  <HeadlineOneButton {...externalProps} />
                  <HeadlineTwoButton {...externalProps} />
                  <HeadlineThreeButton {...externalProps} />
                  <Separator {...externalProps} />
                  <div className={classes.customInlineButton}>
                    <UndoButton {...externalProps}></UndoButton>
                  </div>
                  <div className={classes.customInlineButton}>
                    <RedoButton {...externalProps}></RedoButton>
                  </div>
                  <Separator {...externalProps} />
                  <div className={classes.customInlineButton}>
                    <EmojiSelect></EmojiSelect>
                  </div>
                  { /* <AlignBlockCenterButton {...externalProps} />
                    <AlignBlockLeftButton {...externalProps} />
                    <AlignBlockRightButton {...externalProps} /> */
                  }
                </>
              )
            }
          </Toolbar>
        </div>
        <div className={classes.editor}  onClick={this.focus}>
            <Editor
                editorState={this.state.editorState}
                onChange={this.onChange}
                plugins={plugins}
                ref={(element) => { this.editor = element; }}
                onKeyDown={this.onKeyPressed}
                onTab={this.handleKeyBindings}
                handleDroppedFiles={this.handleDropFiles}
                handlePastedFiles={this.handlePastedFiles}
            />
            <EmojiSuggestions></EmojiSuggestions>
        </div>
        <div className={classes.btnCenter}>
          <UI.Button variant="contained" color="primary" component="span">
                Publish
          </UI.Button>
        </div>
      </div>
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

