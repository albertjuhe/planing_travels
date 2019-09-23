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
        const resp = await fetch(`${baseUrl}api/user/${user}/travels/`);
        return resp.json();
    } catch (error) {
        throw error;
    }
}