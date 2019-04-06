import React from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';

class HomePage extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      channels: [],
      discussions: [],
      showModalCreateDiscussion: false,
    }

    this.onCreateDiscussionClick = this.onCreateDiscussionClick.bind(this);
    this.onCreateDiscussionModalClick = this.onCreateDiscussionModalClick.bind(this);
  }

  componentWillMount() {
    axios
      .get('/forum/api/channels')
      .then(response => {
        this.setState({channels: response.data.data});
      });

    axios
      .get('/forum/api/discussions')
      .then(response => {
        this.setState({discussions: response.data.data});
      });
  }

  onCreateDiscussionClick() {
    this.onCreateDiscussionModalClick(false);
  }

  onCreateDiscussionModalClick(show) {
    this.setState({showModalCreateDiscussion: show || false})
  }

  render() {
    const {
      channels,
      discussions,
      showModalCreateDiscussion,
    } = this.state;

    return(
      <div className="container content">
        <div className="row">
          <div className="col-xs-12 col-md-3">
            <div className="form-group">
              <button
                className="btn btn-primary"
                onClick={() => this.onCreateDiscussionModalClick(true)}
              >Add Discussion</button>
            </div>

            <h4>Channels</h4>
            <ul>
            {channels.map(channel =>
              <li key={channel.id}><Link to={'channels/' + channel.id}>{channel.title}</Link></li>
            )}
            </ul>
          </div>
          <div className="col-xs-12 col-md-9">
            {discussions.length ? '' : 'No Discussions'}

            {discussions.map(discussion =>
              <div key={discussion.id}>
                <div className="row">
                  <div className="col-sm-6">
                    <h2><Link to={'channels/' + discussion.channel_id + '/discussions/' + discussion.id}>{discussion.title}</Link></h2>
                    <p>
                      {discussion.last_reply ? discussion.last_reply.author.name : discussion.author.name}
                      {discussion.last_reply ? ' replied on ' : ' posted on '}
                      {discussion.last_reply ? discussion.last_reply.created_at : discussion.created_at}
                    </p>
                  </div>
                  <div className="col-sm-6 text-right">
                    Views: {discussion.view_count}{' '}
                    Replies: {discussion.reply_count}{' '}
                    Posted in {discussion.channel.title}
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>

        <div className="modal-backdrop show" style={showModalCreateDiscussion ? {} : {display: 'none'}}></div>
        <div className="modal fade show" tabIndex="-1" role="dialog" style={showModalCreateDiscussion ? {display: 'block'} : {}}>
          <div className="modal-dialog" role="document">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">Create Discussion</h5>
                <button type="button" className="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div className="modal-body">
                <form>
                  <div className="form-group">
                    <label htmlFor="title">Title</label>
                    <input className="form-control" id="title" type="text" name="title" />
                  </div>
                  <label htmlFor="content">Content</label>
                  <textarea className="form-control" id="content" name="content"></textarea>
                </form>
              </div>
              <div className="modal-footer">
                <button
                  type="button"
                  className="btn btn-secondary"
                  onClick={() => this.onCreateDiscussionModalClick(false)}
                >Cancel</button>
                <button
                  type="button"
                  className="btn btn-primary"
                  onClick={this.onCreateDiscussionClick}
                >Create Discussion</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default HomePage;