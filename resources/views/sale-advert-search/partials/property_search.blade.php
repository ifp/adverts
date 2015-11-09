<h3>Property Search</h3>

<form method="post" action="/sale-advert-search" class="modify_search">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<div class="form-group">
		<select id="Location" name="lcl[]" class="form-control">
			<option value="" selected="selected">All of France</option>
			<option value="59" id="r_59">Alsace</option>
			<option value="148">__Bas-Rhin</option>
			<option value="149">__Haut-Rhin</option>
			<option value="60" id="r_60">Aquitaine</option>
			<option value="105">__Dordogne</option>
			<option value="114">__Gironde</option>
			<option value="121">__Landes</option>
			<option value="128">__Lot-et-Garonne</option>
			<option value="145">__Pyrénées-Atlantiques</option>
			<option value="61" id="r_61">Auvergne</option>
			<option value="83">__Allier</option>
			<option value="95">__Cantal</option>
			<option value="124">__Haute-Loire</option>
			<option value="144">__Puy-de-Dôme</option>
			<option value="64" id="r_64">Brittany</option>
			<option value="103">__Côtes-d'Armor</option>
			<option value="110">__Finistère</option>
			<option value="116">__Ille-et-Vilaine</option>
			<option value="137">__Morbihan</option>
			<option value="63" id="r_63">Burgundy</option>
			<option value="102">__Côte-d'Or</option>
			<option value="139">__Nièvre</option>
			<option value="152">__Saône-et-Loire</option>
			<option value="170">__Yonne</option>
			<option value="65" id="r_65">Centre</option>
			<option value="98">__Cher</option>
			<option value="109">__Eure-et-Loir</option>
			<option value="117">__Indre</option>
			<option value="118">__Indre-et-Loire</option>
			<option value="122">__Loir-et-Cher</option>
			<option value="126">__Loiret</option>
			<option value="66" id="r_66">Champagne-Ardenne</option>
			<option value="88">__Ardennes</option>
			<option value="90">__Aube</option>
			<option value="133">__Haute-Marne</option>
			<option value="132">__Marne</option>
			<option value="67" id="r_67">Corsica</option>
			<option value="100">__Corse-du-Sud</option>
			<option value="101">__Haute-Corse</option>
			<option value="68" id="r_68">Franche-Comté</option>
			<option value="106">__Doubs</option>
			<option value="151">__Haute-Saône</option>
			<option value="120">__Jura</option>
			<option value="171">__Territoire-de-Belfort</option>
			<option value="71" id="r_71">Languedoc-Roussillon</option>
			<option value="91">__Aude</option>
			<option value="111">__Gard</option>
			<option value="115">__Hérault</option>
			<option value="129">__Lozère</option>
			<option value="147">__Pyrénées-Orientales</option>
			<option value="72" id="r_72">Limousin</option>
			<option value="99">__Corrèze</option>
			<option value="104">__Creuse</option>
			<option value="168">__Haute-Vienne</option>
			<option value="73" id="r_73">Lorraine</option>
			<option value="135">__Meurthe-et-Moselle</option>
			<option value="136">__Meuse</option>
			<option value="138">__Moselle</option>
			<option value="169">__Vosges</option>
			<option value="62" id="r_62">Lower-Normandy</option>
			<option value="94">__Calvados</option>
			<option value="131">__Manche</option>
			<option value="142">__Orne</option>
			<option value="74" id="r_74">Midi-Pyrénées</option>
			<option value="89">__Ariège</option>
			<option value="92">__Aveyron</option>
			<option value="113">__Gers</option>
			<option value="112">__Haute-Garonne</option>
			<option value="146">__Hautes-Pyrénées</option>
			<option value="127">__Lot</option>
			<option value="162">__Tarn</option>
			<option value="163">__Tarn-et-Garonne</option>
			<option value="75" id="r_75">Nord-Pas-de-Calais</option>
			<option value="140">__Nord</option>
			<option value="143">__Pas-de-Calais</option>
			<option value="70" id="r_70">Paris Ile-de-France</option>
			<option value="172">__Essonne</option>
			<option value="173">__Hauts-de-Seine</option>
			<option value="156">__Paris</option>
			<option value="174">__Seine-Saint-Denis</option>
			<option value="158">__Seine-et-Marne</option>
			<option value="176">__Val-d'Oise</option>
			<option value="175">__Val-de-Marne</option>
			<option value="159">__Yvelines</option>
			<option value="76" id="r_76">Pays de la Loire</option>
			<option value="125">__Loire-Atlantique</option>
			<option value="130">__Maine-et-Loire</option>
			<option value="134">__Mayenne</option>
			<option value="153">__Sarthe</option>
			<option value="166">__Vendée</option>
			<option value="77" id="r_77">Picardy</option>
			<option value="82">__Aisne</option>
			<option value="141">__Oise</option>
			<option value="161">__Somme</option>
			<option value="78" id="r_78">Poitou-Charentes</option>
			<option value="96">__Charente</option>
			<option value="97">__Charente-Maritime</option>
			<option value="160">__Deux-Sèvres</option>
			<option value="167">__Vienne</option>
			<option value="79" id="r_79">Provence-Alpes-Côte d'Azur</option>
			<option value="86">__Alpes-Maritimes</option>
			<option value="84">__Alpes-de-Haute-Provence</option>
			<option value="93">__Bouches-du-Rhône</option>
			<option value="85">__Hautes-Alpes</option>
			<option value="164">__Var</option>
			<option value="165">__Vaucluse</option>
			<option value="80" id="r_90">Rhône-Alpes</option>
			<option value="81">__Ain</option>
			<option value="87">__Ardèche</option>
			<option value="107">__Drôme</option>
			<option value="155">__Haute-Savoie</option>
			<option value="119">__Isère</option>
			<option value="123">__Loire</option>
			<option value="150">__Rhône</option>
			<option value="154">__Savoie</option>
			<option value="69" id="r_69">Upper-Normandy</option>
			<option value="108">__Eure</option>
			<option value="157">__Seine-Maritime</option>
		</select>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-xs-6 col-sm-6 col-md-6">
				<input type="text" class="form-control" name="pmn" placeholder="Min Price (Euros)">
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6">
				<input type="text" class="form-control" name="pmx" placeholder="Max Price (Euros)">
			</div>								
		</div>
	</div>
	<div class="form-group">
		<select name="typ[]" id="type" class="form-control">
			<option value="">Property type: All</option>
			<option value="6">Bar / Resturant / Cafe</option>
			<option value="3">Character House</option>
			<option value="1">Flat / Appartment</option>
			<option value="5">Hotel / B&amp;B</option>
			<option value="2">House / Cotttage</option>
			<option value="4">Land</option>
		</select>
	</div>
	<div class="form-group">
		<select name="bedrooms" id="bedrooms" class="form-control">
			<option value="">Number of Bedrooms: Any</option>
			<option value="1">1+ bedrooms</option>
			<option value="2">2+ bedrooms</option>
			<option value="3">3+ bedrooms</option>
			<option value="4">4+ bedrooms</option>
			<option value="5">5+ bedrooms</option>
		</select>
	</div>
	<div class="checkbox">
		<label>
			<input type="checkbox" name="pol"> Swimming Pool
		</label>
	</div>							
	<button type="submit" class="btn btn-primary">Search</button>
	<a href="" class="btn btn-link">New Search</a>
</form>
