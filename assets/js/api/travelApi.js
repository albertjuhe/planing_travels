export const baseUrl = 'http://localhost/planing_travels/public/index.php/';

export const getBestTravels = async({total}) => {
    try {
        return await fetch('http://localhost/planing_travels/public/index.php/api/travels/best/10');
    } catch (error) {
        throw error;
    }
}