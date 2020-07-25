import React, { Component } from "react";
import { MemoryRouter, Route } from 'react-router';
import { Link } from 'react-router-dom';

import * as UI from '../../UI/Pagination/base';

import { makeStyles, withStyles, withTheme } from '@material-ui/core/styles';
import PaginationStyle from '../../UI/Pagination/style';

class Pagination extends React.Component {

  state = {

  }

  getPageCounter(dataLength, maxItem) {
    let n = (dataLength / maxItem);
    const isFloat = Number(n) === n && n % 1 !== 0;
    n += isFloat ? 1 : 0;
    n = ~~n;

    return n;
  }

  render() {
    const classes = this.props.classes;
    const n = this.getPageCounter(this.props.data.length, this.props.maxItem);

    return (
      <MemoryRouter initialEntries={[this.props.baseUrl]} initialIndex={0}>
        <Route>
          {({ location }) => {
            const query = new URLSearchParams(location.search);
            const page = parseInt(query.get('page') || '1', 10);
            const start = this.props.maxItem * (page - 1);

            return (
              <div>
                {
                  this.props.data
                  && this.props.render
                  && this.props.data.slice(start, start + this.props.maxItem).map((item, index) => (
                    this.props.render(item, index)
                  ))
                }
                <div className={classes.pagination}>
                  <UI.Pagination
                    page={page}
                    count={n}
                    renderItem={(item) => (
                      <UI.PaginationItem
                        component={Link}
                        to={`/${this.props.baseUrl}${item.page === 1 ? '' : `?page=${item.page}`}`}
                        {...item}
                      />
                    )}
                  />
                </div>
              </div>
            );
          }}
        </Route>
      </MemoryRouter>
    );
  }
}

export default withStyles(PaginationStyle)(Pagination);