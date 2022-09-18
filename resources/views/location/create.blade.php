<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form action="{{ route('location.store') }}" method="POST">
        @csrf
        <table>
            <tbody>
            <tr>
                <th>
                    address_name
                </th>
                <td>
                    <input type="text" name="address_name" value="">
                </td>
            </tr>
            <tr>
                <th>
                    category_group_code
                </th>
                <td>
                    <input type="text" name="category_group_code" value="">
                </td>
            </tr>
            <tr>
                <th>
                    category_group_name
                </th>
                <td>
                    <input type="text" name="category_group_name" value="">
                </td>
            </tr>
            <tr>
                <th>
                    category_name
                </th>
                <td>
                    <input type="text" name="category_name" value="">
                </td>
            </tr>
            <tr>
                <th>
                    distance
                </th>
                <td>
                    <input type="text" name="distance" value="">
                </td>
            </tr>
            <tr>
                <th>
                    map_id
                </th>
                <td>
                    <input type="text" name="map_id" value="">
                </td>
            </tr>
            <tr>
                <th>
                    phone
                </th>
                <td>
                    <input type="text" name="phone" value="">
                </td>
            </tr>
            <tr>
                <th>
                    place_name
                </th>
                <td>
                    <input type="text" name="place_name" value="">
                </td>
            </tr><tr>
                <th>
                    place_url
                </th>
                <td>
                    <input type="text" name="place_url" value="">
                </td>
            </tr>
            <tr>
                <th>
                    road_address_name
                </th>
                <td>
                    <input type="text" name="road_address_name" value="">
                </td>
            </tr>
            <tr>
                <th>
                    lat
                </th>
                <td>
                    <input type="text" name="lat" value="">
                </td>
            </tr>
            <tr>
                <th>
                    lng
                </th>
                <td>
                    <input type="text" name="lng" value="">
                </td>

            </tbody>
        </table>
        <input type="submit" value="저장">
    </form>
</body>
</html>