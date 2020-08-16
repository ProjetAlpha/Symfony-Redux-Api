import React, { Component } from "react";
import { connect } from "react-redux";
import { makeStyles, withStyles } from '@material-ui/core/styles';
import { convertToRaw, EditorState, AtomicBlockUtils, RichUtils, getDefaultKeyBinding, convertFromRaw, CompositeDecorator, Editor } from "draft-js";

import * as UI from '../../UI/Admin/base';
import Pagination from '../main/Pagination';
import AdminStyle from '../../UI/Admin/style';
import { fetchAllArticle } from '../../actions/Admin';
import draftToHtml from 'draftjs-to-html';
import Search from '../main/Search';

class ArticleList extends React.Component {

    state = {
        articles: [],
        highlightArticles: [],
        loading: true
    }

    componentDidMount() {
        if (!this.props.articles) {
            this.props.fetchAllArticle(this.props.user.id, {
                is_draft: true
            });
        } else {
            this.setState({
                articles: this.props.articles,
                loading: false
            });
        }
    }

    UNSAFE_componentWillReceiveProps(nextProps) {
        if (nextProps.articles !== this.props.articles) {
            this.setState({
                articles: nextProps.articles,
                loading: false
            })
        }
    }

    update(articles) {
        console.log(articles);
        this.setState({
            articles: articles
        })
    }

    reset(articles) {
        this.update(articles);
        this.setState({
            highlightArticles: []
        })
    }


    // match strong, h, i and b tags - standard search
    // match key word - advanced search
    findArticleMatch(search, article, articleIndex) {
        // const tagsRegex = /(?:<strong>|<i>|<b>|<h\d+>)([^<]*)/gm; // filter by tags
        const tagsRegex = /(<strong>|<i>|<b>|<h\d+>|<p>|<blockquote>)([^<]*)/gm; // key word
        const matchs = article.raw_data.match(tagsRegex);
        console.log(matchs);
        let startIndex = 0;
        const target = matchs.find(match => {
            const state = match.toLowerCase().indexOf(search);
            if (-1 !== state)
                startIndex+= match.length;
            return -1 !== state;
        });
        //console.log(startIndex);
        //console.log(target);
        // tags[1]
        const tags = tagsRegex.exec(article.raw_data);
        console.log(tags);
        if (!tags) return false;

        const match = tags[1].replace(/\s\s+/g, ' ').trim();
        const withoutNewLines = match.replace(/&nbsp;/g, '');
        const index = withoutNewLines.toLowerCase().indexOf(search);
        if (-1 == index) return false;

        //console.log(tags);

        const text = tags[1].replace(/&nbsp;/g, '');
        const start = tags[0].indexOf(text) + tags.index;
        const end = start + text.length;

        const innerSearch = article.raw_data.slice(start, end);
        const searchStart = innerSearch.indexOf(search);

        let i = searchStart;
        let j = 0;
        while (i < innerSearch.length && j < search.length && innerSearch[i++] == search[j++])
            ;

        const articleHighlight = article.raw_data.slice(0, start + searchStart)
                                + '<span class="hightlight-search">'
                                + article.raw_data.slice(start + searchStart, start + searchStart + j)
                                + '</span>'
                                + article.raw_data.slice(start + searchStart + j, article.raw_data.length);

        this.setState((prevState) => ({
            highlightArticles: [...prevState.highlightArticles, articleHighlight]
        }))
        //console.log(articleHighlight);
        return true;
        // const styleRegex = /<.+?style=\s*\"\s*(\S*)\s*\"\s*>([^<]*)/gm;
        // const style = styleRegex.exec(article.raw_data);
    }

    render() {
        const classes = this.props.classes;

        return (
            <div className={classes.root}>
                <UI.List className={classes.root}>
                    <UI.ListItem className={classes.searchItem}>
                        <Search
                            filterData={(search, article, index) => this.findArticleMatch(search, article, index)}
                            data={Array.isArray(this.state.articles) ? this.state.articles : []}
                            update={(articles) => this.update(articles)}
                            reset={(articles) => this.reset(articles)}
                            isLoading={this.state.loading}
                        />
                    </UI.ListItem>
                    {this.state.articles && <Pagination baseUrl={'/articles'} maxItem={5} data={this.state.articles} render={
                        (article, index, position) => (
                            <div key={index}>
                                <UI.ListItem key={index}>

                                    { /*this.state.editors[position] &&
                                            <Editor editorState={this.state.editors[position]}
                                                onChange={this.onChange} readOnly/>*/
                                    }
                                    <div dangerouslySetInnerHTML={{ __html: this.state.highlightArticles[position] || article.raw_data }}></div>

                                </UI.ListItem>
                                <UI.Divider component="li" />
                            </div>
                        )
                    }
                    />
                    }
                </UI.List>
            </div>
        );
    }
}

const mapStateToProps = state => {
    return {
        error: state.Error.error,
        user: state.Authentification.user,
        users: state.Admin.users,
        articles: state.Admin.articles
    };
};

const style = withStyles(AdminStyle)(ArticleList);

export default connect(mapStateToProps, { fetchAllArticle })(style);