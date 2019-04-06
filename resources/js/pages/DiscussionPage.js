import React from 'react';
import axios from 'axios';

class DiscussionPage extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      discussion: null,
      reply: '',
    };

    this.onReplyInputChange = this.onReplyInputChange.bind(this);
    this.onCreateReplyClick = this.onCreateReplyClick.bind(this);
  }

  componentWillMount() {
    const {params} = this.props.match;
    const channel = params.channel;
    const discussion = params.discussion;

    axios
      .get('/forum/api/channels/' + channel + '/discussions/' + discussion)
      .then(response => {
        this.setState({discussion: response.data});
      });
  }

  onCreateReplyClick()
  {
    const {reply} = this.state;
    const {params} = this.props.match;
    const discussion = params.discussion;

    axios
      .post(
        '/forum/api/discussions/' + discussion + '/replies',
        {content: reply}
      )
      .then(response => {
        this.setState({reply: ''});
      });
  }

  onReplyInputChange(event)
  {
    this.setState({reply: event.target.value});
  }

  render() {
    const {
      discussion,
      reply,
    } = this.state;

    if (discussion === null || typeof discussion !== 'object') {
      return (
        <div className="container content">
          Discussion not found.
        </div>
      )
    }

    return (
      <div className="container content">
        <div className="form-group">
          <div className="float-right">
            <span className="badge badge-pill badge-primary">{discussion.channel.title}</span>
          </div>

          <strong>{discussion.author.name}</strong> posted on {discussion.created_at}
          <h1>{discussion.title}</h1>
          {discussion.content}
        </div>

        <div id="replies">
          {discussion.replies.map(reply =>
            <div className="card mb-3" key={reply.id}>
              <div className="card-body">
                <strong>{reply.author.name}</strong> posted on {reply.created_at}
                <div>{reply.content}</div>
              </div>
            </div>
          )}
        </div>

        <h3>Add Reply</h3>

        <div className="form-group">
          <label>Reply</label>
          <textarea
            className="form-control"
            name="content"
            onChange={e => this.onReplyInputChange(e)}
            value={reply}
          ></textarea>
        </div>
        <button
          className="btn btn-primary"
          onClick={this.onCreateReplyClick}
        >Add Reply</button>
      </div>
    )
  }
}

export default DiscussionPage;