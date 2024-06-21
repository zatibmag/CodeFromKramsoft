class Client {
    #baseUrl;

    #_get(collectionName) {
        let url;

        switch (collectionName) {
            case 'lists':
                url = 'lists';
                break;
            case 'cards':
                url = 'tasks';
                break;
            default:
                throw new Error('Unknown collection name');
        }

        return fetch(this.#baseUrl + url);
    };

    constructor(baseUrl = '/') {
        this.#baseUrl = baseUrl;
    }

    async cards() {
        const response = await this.#_get('cards');

        return await response.json();
    }

    async lists() {
        const response = await this.#_get('lists');

        return await response.json();
    }
}

export default Client;
