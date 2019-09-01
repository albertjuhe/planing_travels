export const baseUrl = 'http://localhost/planing_travels/public/index.php/';

export const getBestTravels = async({total}) => {
    try {
        const resp = await fetch('http://localhost/planing_travels/public/index.php/api/travels/best/10');
        return resp.json();
    } catch (error) {
        throw error;
    }
}