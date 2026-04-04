import React, { Component } from 'react';
import { shareTravel } from '../api/travelApi';

class ShareTravel extends Component {
    constructor(props) {
        super(props);
        this.state = {
            username: '',
            status: null,
            error: null,
            loading: false,
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(e) {
        this.setState({ username: e.target.value, status: null, error: null });
    }

    async handleSubmit(e) {
        e.preventDefault();
        const { username } = this.state;
        const { travelId } = this.props;

        if (!username.trim()) return;

        this.setState({ loading: true, status: null, error: null });

        try {
            const result = await shareTravel(travelId, username.trim());
            if (result.shared) {
                this.setState({ status: `Travel shared with ${result.username}`, username: '' });
            } else {
                this.setState({ error: result.error || 'Could not share travel' });
            }
        } catch (err) {
            this.setState({ error: 'Request failed' });
        } finally {
            this.setState({ loading: false });
        }
    }

    render() {
        const { username, status, error, loading } = this.state;

        return (
            <div className="share-travel">
                <h4>Share travel</h4>
                <form onSubmit={this.handleSubmit}>
                    <div className="form-group">
                        <label htmlFor="share-username">Username</label>
                        <input
                            id="share-username"
                            type="text"
                            className="form-control"
                            value={username}
                            onChange={this.handleChange}
                            placeholder="Enter username"
                            disabled={loading}
                        />
                    </div>
                    <button
                        type="submit"
                        className="btn btn-primary"
                        disabled={loading || !username.trim()}
                    >
                        {loading ? 'Sharing…' : 'Share'}
                    </button>
                </form>
                {status && <p className="text-success" style={{ marginTop: 8 }}>{status}</p>}
                {error  && <p className="text-danger"  style={{ marginTop: 8 }}>{error}</p>}
            </div>
        );
    }
}

export default ShareTravel;
