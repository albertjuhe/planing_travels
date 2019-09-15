import React from 'react'
import {baseUrl} from "../../api/travelApi";

const Travel = ({data}) =>  (
    <tr>
        <td><a href={`${baseUrl}en/travel/${data.slug}`}>{data.title}</a><br/>{data.description}</td>
        <td>{data.startAt.date} to {data.endAt.date}</td>
        <td><span className="glyphicon glyphicon-star" aria-hidden="true"/>{data.stars}</td>
        <td><span className="glyphicon glyphicon-eye-open" aria-hidden="true"/>{data.watch}</td>
        <td><span className="glyphicon glyphicon-random" aria-hidden="true"/> 0</td>
        <td>{data.username}</td>
    </tr>
);

export default Travel;