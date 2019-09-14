export const baseUrl = '/';

export const getBestTravels = async(total) => {
    try {
        const resp = await fetch(`${baseUrl}api/travels/best/${total}`);
        return resp.json();
    } catch (error) {
        throw error;
    }
};

export const getTravelsByUser = async(user) => {
    try {
        const resp = await fetch(`${baseUrl}api/user/1/travels/`);
        //const resp = await fetch(`${baseUrl}api/travels/best/10`);
        return resp.json();
    } catch (error) {
        throw error;
    }
}