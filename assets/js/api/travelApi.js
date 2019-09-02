export const baseUrl = 'http://localhost/planing_travels/public/index.php/';

export const getBestTravels = async(total) => {
    try {
        const resp = await fetch(`${baseUrl}api/travels/best/${total}`);
        return resp.json();
    } catch (error) {
        throw error;
    }
}