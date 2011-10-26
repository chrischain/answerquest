package model;

import java.io.Serializable;

import java.lang.StringBuilder;

import javax.persistence.Id;
import javax.persistence.NamedQueries;
import javax.persistence.NamedQuery;

import javax.xml.bind.annotation.*;

import javax.persistence.*;

/**
 */

@Entity
@NamedQueries({
		@NamedQuery(name = "findAllSeriess", query = "select mySeries from Series mySeries"),
		@NamedQuery(name = "findSeriesByActive", query = "select mySeries from Series mySeries where mySeries.active = ?1"),
		@NamedQuery(name = "findSeriesByAllowAnonymous", query = "select mySeries from Series mySeries where mySeries.allowAnonymous = ?1"),
		@NamedQuery(name = "findSeriesByCode", query = "select mySeries from Series mySeries where mySeries.code = ?1"),
		@NamedQuery(name = "findSeriesByCodeContaining", query = "select mySeries from Series mySeries where mySeries.code like ?1"),
		@NamedQuery(name = "findSeriesByDescription", query = "select mySeries from Series mySeries where mySeries.description = ?1"),
		@NamedQuery(name = "findSeriesByDescriptionContaining", query = "select mySeries from Series mySeries where mySeries.description like ?1"),
		@NamedQuery(name = "findSeriesById", query = "select mySeries from Series mySeries where mySeries.id = ?1"),
		@NamedQuery(name = "findSeriesByMaxResponses", query = "select mySeries from Series mySeries where mySeries.maxResponses = ?1"),
		@NamedQuery(name = "findSeriesByMinResponses", query = "select mySeries from Series mySeries where mySeries.minResponses = ?1"),
		@NamedQuery(name = "findSeriesByName", query = "select mySeries from Series mySeries where mySeries.name = ?1"),
		@NamedQuery(name = "findSeriesByNameContaining", query = "select mySeries from Series mySeries where mySeries.name like ?1"),
		@NamedQuery(name = "findSeriesByPrimaryKey", query = "select mySeries from Series mySeries where mySeries.id = ?1"),
		@NamedQuery(name = "findSeriesByType", query = "select mySeries from Series mySeries where mySeries.type = ?1") })
@Table(catalog = "HACKATHON", name = "series")
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(namespace = "Hackathon/com/model", name = "Series")
@XmlRootElement
public class Series implements Serializable {
	private static final long serialVersionUID = 1L;

	/**
	 */

	@Column(name = "id", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@Id
	@XmlElement
	Integer id;
	/**
	 */

	@Column(name = "name", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	String name;
	/**
	 */

	@Column(name = "code", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	String code;
	/**
	 */

	@Column(name = "description", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	String description;
	/**
	 */

	@Column(name = "min_responses", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	Integer minResponses;
	/**
	 */

	@Column(name = "max_responses", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	Integer maxResponses;
	/**
	 */

	@Column(name = "type", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	Integer type;
	/**
	 */

	@Column(name = "allow_anonymous", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	Boolean allowAnonymous;
	/**
	 */

	@Column(name = "active", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	Boolean active;

	/**
	 */
	public void setId(Integer id) {
		this.id = id;
	}

	/**
	 */
	public Integer getId() {
		return this.id;
	}

	/**
	 */
	public void setName(String name) {
		this.name = name;
	}

	/**
	 */
	public String getName() {
		return this.name;
	}

	/**
	 */
	public void setCode(String code) {
		this.code = code;
	}

	/**
	 */
	public String getCode() {
		return this.code;
	}

	/**
	 */
	public void setDescription(String description) {
		this.description = description;
	}

	/**
	 */
	public String getDescription() {
		return this.description;
	}

	/**
	 */
	public void setMinResponses(Integer minResponses) {
		this.minResponses = minResponses;
	}

	/**
	 */
	public Integer getMinResponses() {
		return this.minResponses;
	}

	/**
	 */
	public void setMaxResponses(Integer maxResponses) {
		this.maxResponses = maxResponses;
	}

	/**
	 */
	public Integer getMaxResponses() {
		return this.maxResponses;
	}

	/**
	 */
	public void setType(Integer type) {
		this.type = type;
	}

	/**
	 */
	public Integer getType() {
		return this.type;
	}

	/**
	 */
	public void setAllowAnonymous(Boolean allowAnonymous) {
		this.allowAnonymous = allowAnonymous;
	}

	/**
	 */
	public Boolean getAllowAnonymous() {
		return this.allowAnonymous;
	}

	/**
	 */
	public void setActive(Boolean active) {
		this.active = active;
	}

	/**
	 */
	public Boolean getActive() {
		return this.active;
	}

	/**
	 */
	public Series() {
	}

	/**
	 * Copies the contents of the specified bean into this bean.
	 *
	 */
	public void copy(Series that) {
		setId(that.getId());
		setName(that.getName());
		setCode(that.getCode());
		setDescription(that.getDescription());
		setMinResponses(that.getMinResponses());
		setMaxResponses(that.getMaxResponses());
		setType(that.getType());
		setAllowAnonymous(that.getAllowAnonymous());
		setActive(that.getActive());
	}

	/**
	 * Returns a textual representation of a bean.
	 *
	 */
	public String toString() {

		StringBuilder buffer = new StringBuilder();

		buffer.append("id=[").append(id).append("] ");
		buffer.append("name=[").append(name).append("] ");
		buffer.append("code=[").append(code).append("] ");
		buffer.append("description=[").append(description).append("] ");
		buffer.append("minResponses=[").append(minResponses).append("] ");
		buffer.append("maxResponses=[").append(maxResponses).append("] ");
		buffer.append("type=[").append(type).append("] ");
		buffer.append("allowAnonymous=[").append(allowAnonymous).append("] ");
		buffer.append("active=[").append(active).append("] ");

		return buffer.toString();
	}

	/**
	 */
	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = (int) (prime * result + ((id == null) ? 0 : id.hashCode()));
		return result;
	}

	/**
	 */
	public boolean equals(Object obj) {
		if (obj == this)
			return true;
		if (!(obj instanceof Series))
			return false;
		Series equalCheck = (Series) obj;
		if ((id == null && equalCheck.id != null) || (id != null && equalCheck.id == null))
			return false;
		if (id != null && !id.equals(equalCheck.id))
			return false;
		return true;
	}
}
